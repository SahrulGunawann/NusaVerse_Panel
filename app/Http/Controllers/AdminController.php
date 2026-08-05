<?php

namespace App\Http\Controllers;

use App\Models\Heritage;
use App\Models\Category;
use App\Models\Province;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Timeline;
use App\Models\TimelineEvent;
use App\Models\Hotspot;
use App\Models\HotspotItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalHeritages = Heritage::count();
        $totalQuizzes = Quiz::count();
        $totalCategories = Category::count();
        $totalProvinces = Province::count();

        $heritages = Heritage::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalHeritages', 'totalQuizzes', 'totalCategories', 'totalProvinces', 'heritages'));
    }

    // HERITAGES CRUD
    public function heritagesIndex(Request $request)
    {
        try {
            Heritage::where('model_3d_url', 'assets/models/model.glb')
                ->where('id', '!=', 'heritage_4')
                ->update(['model_3d_url' => '']);
        } catch (\Exception $e) {}

        $query = Heritage::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('category_name', 'LIKE', "%{$search}%")
                  ->orWhere('province_name', 'LIKE', "%{$search}%");
            });
        }

        $heritages = $query->latest()->paginate(10)->withQueryString();
        return view('admin.heritages.index', compact('heritages'));
    }

    public function heritagesCreate()
    {
        $categories = Category::all();
        $provinces = Province::all();
        return view('admin.heritages.create', compact('categories', 'provinces'));
    }

    public function heritagesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_name' => 'required|string',
            'province_name' => 'required|string',
            'full_description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'cover_image' => 'nullable|file|max:10240',
            'model_3d' => 'nullable|file|max:102400',
        ]);

        if ($request->hasFile('cover_image') && !$request->file('cover_image')->isValid()) {
            return back()->with('error', 'Gagal mengunggah Gambar Sampul: Ukuran file melebihi batas upload PHP cPanel Anda (' . ini_get('upload_max_filesize') . '). Silakan naikkan upload_max_filesize di cPanel MultiPHP INI Editor.')->withInput();
        }

        if ($request->hasFile('model_3d') && !$request->file('model_3d')->isValid()) {
            return back()->with('error', 'Gagal mengunggah File 3D Model: Ukuran file melebihi batas upload PHP cPanel Anda (' . ini_get('upload_max_filesize') . '). Silakan naikkan upload_max_filesize di cPanel MultiPHP INI Editor.')->withInput();
        }

        $slug = Str::slug($request->name);
        $id = 'heritage_' . Str::random(8);

        $timelineId = 'timeline_' . $slug;
        $hotspotId = 'hotspot_' . $slug;

        $additionalSections = [];
        if ($request->filled('additional_sections')) {
            foreach ($request->input('additional_sections') as $sec) {
                if (!empty($sec['title']) || !empty($sec['content'])) {
                    $additionalSections[] = [
                        'title' => $sec['title'] ?? '',
                        'content' => $sec['content'] ?? '',
                    ];
                }
            }
        }

        $sources = [];
        if ($request->filled('sources')) {
            foreach ($request->input('sources') as $src) {
                if (!empty($src['name']) || !empty($src['url'])) {
                    $sources[] = [
                        'name' => $src['name'] ?? '',
                        'url' => $src['url'] ?? '',
                    ];
                }
            }
        } elseif ($request->filled('source_name')) {
            $sources[] = [
                'name' => $request->source_name,
                'url' => $request->source_url ?? '',
            ];
        }

        try {
            $this->ensureHeritageColumnsExist();

            $coverImagePath = 'assets/images/placeholders/placeholder_heritage.jpg';
            if ($request->hasFile('cover_image')) {
                try {
                    $imgDir = storage_path('app/public/images');
                    if (!file_exists($imgDir)) {
                        @mkdir($imgDir, 0777, true);
                    }
                    $file = $request->file('cover_image');
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                    $filename = Str::random(40) . '.' . $ext;
                    try {
                        $coverImagePath = $file->storeAs('images', $filename, 'public');
                    } catch (\Throwable $e) {
                        $file->move($imgDir, $filename);
                        $coverImagePath = 'images/' . $filename;
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Cover Image Upload Failed: ' . $e->getMessage());
                    $coverImagePath = 'assets/images/placeholders/placeholder_heritage.jpg';
                }
            }

            $model3dUrl = '';
            if ($request->hasFile('model_3d')) {
                try {
                    $modelDir = storage_path('app/public/models');
                    if (!file_exists($modelDir)) {
                        @mkdir($modelDir, 0777, true);
                    }
                    $file = $request->file('model_3d');
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'glb');
                    $filename = Str::random(40) . '.' . $ext;
                    try {
                        $model3dUrl = $file->storeAs('models', $filename, 'public');
                    } catch (\Throwable $e) {
                        $file->move($modelDir, $filename);
                        $model3dUrl = 'models/' . $filename;
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('3D Model Upload Failed: ' . $e->getMessage());
                    $model3dUrl = '';
                }
            }

            Heritage::create([
                'id' => $id,
                'name' => $request->name,
                'slug' => $slug,
                'category_name' => $request->category_name,
                'category_id' => Str::slug($request->category_name),
                'province_name' => $request->province_name,
                'province_id' => Str::slug($request->province_name),
                'short_description' => Str::limit($request->full_description, 120),
                'full_description' => $request->full_description,
                'additional_sections' => $additionalSections,
                'source_name' => $sources[0]['name'] ?? $request->source_name,
                'source_url' => $sources[0]['url'] ?? $request->source_url,
                'sources' => $sources,
                'cover_image' => $coverImagePath,
                'model_3d_url' => $model3dUrl,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'opening_hours' => '08.00 - 17.00 WIB',
                'ticket_price' => 'Gratis',
                'is_featured' => $request->boolean('is_featured'),
                'timeline_id' => $timelineId,
                'hotspot_id' => $hotspotId,
            ]);

            // Create Timeline & Events if provided
            if ($request->filled('timeline_events')) {
                $timeline = Timeline::create([
                    'id' => $timelineId,
                    'heritage_id' => $id,
                    'heritage_slug' => $slug,
                    'title' => 'Linimasa Sejarah ' . $request->name,
                ]);

                foreach ($request->timeline_events as $index => $event) {
                    if (!empty($event['year']) || !empty($event['title'])) {
                        TimelineEvent::create([
                            'timeline_id' => $timeline->id,
                            'year' => $event['year'] ?? '',
                            'title' => $event['title'] ?? '',
                            'description' => $event['description'] ?? '',
                            'order' => $index,
                        ]);
                    }
                }
            }

            // Create 3D Hotspots if provided
            if ($request->filled('hotspot_items')) {
                $hotspot = Hotspot::create([
                    'id' => $hotspotId,
                    'heritage_id' => $id,
                    'heritage_slug' => $slug,
                    'title' => 'Titik Informasi 3D ' . $request->name,
                ]);

                foreach ($request->hotspot_items as $index => $item) {
                    if (!empty($item['title'])) {
                        HotspotItem::create([
                            'hotspot_id' => $hotspot->id,
                            'title' => $item['title'] ?? '',
                            'description' => $item['description'] ?? '',
                            'x' => (float)($item['x'] ?? 0),
                            'y' => (float)($item['y'] ?? 0),
                            'z' => (float)($item['z'] ?? 0),
                            'order' => $index,
                        ]);
                    }
                }
            }

            // Auto-create empty Quiz record for new Heritage
            Quiz::firstOrCreate(
                ['heritage_slug' => $slug],
                [
                    'id' => 'quiz_' . $slug,
                    'category' => $request->category_name ?? 'Sejarah & Budaya',
                    'title' => 'Kuis ' . $request->name,
                    'description' => 'Uji pengetahuan dan wawasan Anda tentang ' . $request->name . '.',
                    'passing_score' => 70,
                ]
            );

            return redirect()->route('admin.heritages.index')->with('success', 'Cagar Budaya berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan Cagar Budaya: ' . $e->getMessage())->withInput();
        }
    }

    public function heritagesEdit($id)
    {
        $heritage = Heritage::with(['timeline.events', 'hotspot.items'])->findOrFail($id);
        $categories = Category::all();
        $provinces = Province::all();
        return view('admin.heritages.edit', compact('heritage', 'categories', 'provinces'));
    }

    public function heritagesUpdate(Request $request, $id)
    {
        $heritage = Heritage::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_name' => 'required|string',
            'province_name' => 'required|string',
            'full_description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'cover_image' => 'nullable|file|max:10240',
            'model_3d' => 'nullable|file|max:102400',
        ]);

        if ($request->hasFile('cover_image') && !$request->file('cover_image')->isValid()) {
            return back()->with('error', 'Gagal memperbarui Gambar Sampul: Ukuran file melebihi batas upload PHP cPanel Anda (' . ini_get('upload_max_filesize') . '). Silakan naikkan upload_max_filesize di cPanel MultiPHP INI Editor.')->withInput();
        }

        if ($request->hasFile('model_3d') && !$request->file('model_3d')->isValid()) {
            return back()->with('error', 'Gagal memperbarui File 3D Model: Ukuran file melebihi batas upload PHP cPanel Anda (' . ini_get('upload_max_filesize') . '). Silakan naikkan upload_max_filesize di cPanel MultiPHP INI Editor.')->withInput();
        }

        try {
            $this->ensureHeritageColumnsExist();

            if ($request->hasFile('cover_image')) {
                try {
                    $imgDir = storage_path('app/public/images');
                    if (!file_exists($imgDir)) {
                        @mkdir($imgDir, 0777, true);
                    }
                    $file = $request->file('cover_image');
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                    $filename = Str::random(40) . '.' . $ext;
                    try {
                        $heritage->cover_image = $file->storeAs('images', $filename, 'public');
                    } catch (\Throwable $e) {
                        $file->move($imgDir, $filename);
                        $heritage->cover_image = 'images/' . $filename;
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Update Cover Failed: ' . $e->getMessage());
                }
            }

            if ($request->hasFile('model_3d')) {
                try {
                    $modelDir = storage_path('app/public/models');
                    if (!file_exists($modelDir)) {
                        @mkdir($modelDir, 0777, true);
                    }
                    $file = $request->file('model_3d');
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'glb');
                    $filename = Str::random(40) . '.' . $ext;
                    try {
                        $heritage->model_3d_url = $file->storeAs('models', $filename, 'public');
                    } catch (\Throwable $e) {
                        $file->move($modelDir, $filename);
                        $heritage->model_3d_url = 'models/' . $filename;
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Update 3D Model Failed: ' . $e->getMessage());
                }
            }

            $additionalSections = [];
            if ($request->filled('additional_sections')) {
                foreach ($request->input('additional_sections') as $sec) {
                    if (!empty($sec['title']) || !empty($sec['content'])) {
                        $additionalSections[] = [
                            'title' => $sec['title'] ?? '',
                            'content' => $sec['content'] ?? '',
                        ];
                    }
                }
            }

            $sources = [];
            if ($request->filled('sources')) {
                foreach ($request->input('sources') as $src) {
                    if (!empty($src['name']) || !empty($src['url'])) {
                        $sources[] = [
                            'name' => $src['name'] ?? '',
                            'url' => $src['url'] ?? '',
                        ];
                    }
                }
            } elseif ($request->filled('source_name')) {
                $sources[] = [
                    'name' => $request->source_name,
                    'url' => $request->source_url ?? '',
                ];
            }

            $heritage->name = $request->name;
            $heritage->category_name = $request->category_name;
            $heritage->category_id = Str::slug($request->category_name);
            $heritage->province_name = $request->province_name;
            $heritage->province_id = Str::slug($request->province_name);
        $heritage->short_description = Str::limit($request->full_description, 120);
        $heritage->full_description = $request->full_description;
        $heritage->additional_sections = $additionalSections;
        $heritage->source_name = $sources[0]['name'] ?? $request->source_name;
        $heritage->source_url = $sources[0]['url'] ?? $request->source_url;
        $heritage->sources = $sources;
        $heritage->latitude = $request->latitude;
        $heritage->longitude = $request->longitude;
        $heritage->is_featured = $request->boolean('is_featured');
        $heritage->save();

        // Update Timeline & Events
        $timelineId = $heritage->timeline_id ?: 'timeline_' . $heritage->slug;
        $timeline = Timeline::firstOrCreate(
            ['id' => $timelineId],
            [
                'heritage_id' => $heritage->id,
                'heritage_slug' => $heritage->slug,
                'title' => 'Linimasa Sejarah ' . $heritage->name,
            ]
        );

        TimelineEvent::where('timeline_id', $timeline->id)->delete();
        if ($request->filled('timeline_events')) {
            foreach ($request->timeline_events as $index => $event) {
                if (!empty($event['year']) || !empty($event['title'])) {
                    TimelineEvent::create([
                        'timeline_id' => $timeline->id,
                        'year' => $event['year'] ?? '',
                        'title' => $event['title'] ?? '',
                        'description' => $event['description'] ?? '',
                        'order' => $index,
                    ]);
                }
            }
        }

        // Update Hotspot & HotspotItems
        $hotspotId = $heritage->hotspot_id ?: 'hotspot_' . $heritage->slug;
        $hotspot = Hotspot::firstOrCreate(
            ['id' => $hotspotId],
            [
                'heritage_id' => $heritage->id,
                'heritage_slug' => $heritage->slug,
                'title' => 'Titik Informasi 3D ' . $heritage->name,
            ]
        );

        HotspotItem::where('hotspot_id', $hotspot->id)->delete();
        if ($request->filled('hotspot_items')) {
            foreach ($request->hotspot_items as $index => $item) {
                if (!empty($item['title'])) {
                    HotspotItem::create([
                        'hotspot_id' => $hotspot->id,
                        'title' => $item['title'] ?? '',
                        'description' => $item['description'] ?? '',
                        'x' => (float)($item['x'] ?? 0),
                        'y' => (float)($item['y'] ?? 0),
                        'z' => (float)($item['z'] ?? 0),
                        'order' => $index,
                    ]);
                }
            }
        }

            return redirect()->route('admin.heritages.index')->with('success', 'Data Cagar Budaya berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui Cagar Budaya: ' . $e->getMessage())->withInput();
        }
    }

    public function heritagesDestroy($id)
    {
        $heritage = Heritage::findOrFail($id);
        $heritage->delete();
        return redirect()->route('admin.heritages.index')->with('success', 'Cagar Budaya berhasil dihapus.');
    }

    // QUIZZES CRUD
    public function quizzesIndex(Request $request)
    {
        $query = Quiz::with('questions');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $quizzes = $query->latest()->paginate(10)->withQueryString();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function quizzesCreate()
    {
        $heritages = Heritage::all();
        $categories = Category::all();
        return view('admin.quizzes.create', compact('heritages', 'categories'));
    }

    public function quizzesStore(Request $request)
    {
        $quizMode = $request->input('quiz_mode', 'heritage');
        $quizId = 'quiz_' . Str::random(8);

        if ($quizMode === 'heritage') {
            $request->validate(['heritage_slug' => 'required|string']);
            $heritage = Heritage::where('slug', $request->heritage_slug)->first();
            $title = $heritage ? 'Kuis ' . $heritage->name : ($request->title ?? 'Kuis Cagar Budaya');
            $category = $heritage ? ($heritage->category_name ?? 'Sejarah & Budaya') : ($request->category ?? 'Sejarah & Budaya');
            $description = $heritage ? 'Uji pengetahuan dan wawasan Anda tentang ' . $heritage->name . '.' : ($request->description ?? 'Kuis Sejarah');
            $heritageSlug = $request->heritage_slug;
            $quizId = 'quiz_' . $heritageSlug;
        } else {
            $request->validate(['title' => 'required|string']);
            $title = $request->title;
            $category = is_array($request->categories) ? implode(', ', $request->categories) : ($request->category ?? 'Tantangan Custom');
            $description = $request->description ?? 'Kuis tantangan gabungan kuis cagar budaya.';
            $heritageSlug = '';
        }

        $quiz = Quiz::updateOrCreate(
            ['id' => $quizId],
            [
                'title' => $title,
                'category' => $category,
                'heritage_slug' => $heritageSlug,
                'description' => $description,
                'passing_score' => 70,
            ]
        );

        if ($request->filled('questions')) {
            QuizQuestion::where('quiz_id', $quiz->id)->delete();

            foreach ($request->questions as $index => $qData) {
                if (!empty($qData['question'])) {
                    QuizQuestion::create([
                        'id' => 'q_' . Str::random(8),
                        'quiz_id' => $quiz->id,
                        'question' => $qData['question'],
                        'options' => array_values($qData['options'] ?? ['A', 'B', 'C', 'D']),
                        'correct_index' => (int)($qData['correct_index'] ?? 0),
                        'explanation' => $qData['explanation'] ?? '',
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.quizzes.index')->with('success', 'Kuis berhasil disimpan!');
    }

    public function quizzesEdit($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        $heritages = Heritage::all();
        $categories = Category::all();
        return view('admin.quizzes.edit', compact('quiz', 'heritages', 'categories'));
    }

    public function quizzesUpdate(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $quizMode = $request->input('quiz_mode', !empty($quiz->heritage_slug) ? 'heritage' : 'custom');

        if ($quizMode === 'heritage') {
            $request->validate(['heritage_slug' => 'required|string']);
            $heritage = Heritage::where('slug', $request->heritage_slug)->first();
            $title = $heritage ? 'Kuis ' . $heritage->name : $quiz->title;
            $category = $heritage ? ($heritage->category_name ?? 'Sejarah & Budaya') : $quiz->category;
            $description = $heritage ? 'Uji pengetahuan dan wawasan Anda tentang ' . $heritage->name . '.' : $quiz->description;
            $heritageSlug = $request->heritage_slug;
        } else {
            $request->validate(['title' => 'required|string']);
            $title = $request->title;
            $category = is_array($request->categories) ? implode(', ', $request->categories) : ($request->category ?? 'Tantangan Custom');
            $description = $request->description ?? 'Kuis tantangan gabungan kuis cagar budaya.';
            $heritageSlug = '';
        }

        $quiz->update([
            'title' => $title,
            'category' => $category,
            'heritage_slug' => $heritageSlug,
            'description' => $description,
            'passing_score' => 70,
        ]);

        if ($request->has('questions')) {
            QuizQuestion::where('quiz_id', $quiz->id)->delete();

            foreach ($request->questions as $index => $qData) {
                if (!empty($qData['question'])) {
                    QuizQuestion::create([
                        'id' => 'q_' . Str::random(8),
                        'quiz_id' => $quiz->id,
                        'question' => $qData['question'],
                        'options' => array_values($qData['options'] ?? ['A', 'B', 'C', 'D']),
                        'correct_index' => (int)($qData['correct_index'] ?? 0),
                        'explanation' => $qData['explanation'] ?? '',
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.quizzes.index')->with('success', 'Kuis berhasil diperbarui!');
    }

    public function quizzesDestroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        QuizQuestion::where('quiz_id', $quiz->id)->delete();
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Kuis berhasil dihapus.');
    }

    // CATEGORIES CRUD
    public function categoriesIndex(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $categories = $query->latest()->paginate(10)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function categoriesCreate()
    {
        return view('admin.categories.create');
    }

    public function categoriesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = Str::slug($request->name);
        $id = $slug;

        Category::create([
            'id' => $id,
            'name' => $request->name,
            'slug' => $slug,
            'icon' => $request->input('icon', 'category'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function categoriesEdit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function categoriesUpdate(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = Str::slug($request->name);

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'icon' => $request->input('icon', 'category'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function categoriesDestroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // PROVINCES CRUD
    public function provincesIndex(Request $request)
    {
        $query = Province::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $provinces = $query->latest()->paginate(10)->withQueryString();
        return view('admin.provinces.index', compact('provinces'));
    }

    public function provincesCreate()
    {
        return view('admin.provinces.create');
    }

    public function provincesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = Str::slug($request->name);
        $id = $slug;

        if (Schema::hasColumn('provinces', 'island')) {
            try {
                Schema::table('provinces', function (Blueprint $table) {
                    $table->string('island')->nullable()->change();
                });
            } catch (\Exception $e) {}
        }

        Province::create([
            'id' => $id,
            'name' => $request->name,
            'slug' => $slug,
            'island' => $request->input('island') ?: 'Indonesia',
        ]);

        return redirect()->route('admin.provinces.index')->with('success', 'Provinsi berhasil ditambahkan!');
    }

    public function provincesEdit($id)
    {
        $province = Province::findOrFail($id);
        return view('admin.provinces.edit', compact('province'));
    }

    public function provincesUpdate(Request $request, $id)
    {
        $province = Province::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = Str::slug($request->name);

        $province->update([
            'name' => $request->name,
            'slug' => $slug,
            'island' => $request->input('island') ?: ($province->island ?? 'Indonesia'),
        ]);

        return redirect()->route('admin.provinces.index')->with('success', 'Provinsi berhasil diperbarui!');
    }

    public function provincesDestroy($id)
    {
        $province = Province::findOrFail($id);
        $province->delete();
        return redirect()->route('admin.provinces.index')->with('success', 'Provinsi berhasil dihapus.');
    }

    public function resetDb()
    {
        try {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            \App\Models\Heritage::truncate();
            \App\Models\Category::truncate();
            \App\Models\QuizQuestion::truncate();
            \App\Models\Quiz::truncate();
            \App\Models\TimelineEvent::truncate();
            \App\Models\Timeline::truncate();
            \App\Models\HotspotItem::truncate();
            \App\Models\Hotspot::truncate();
            \App\Models\Province::truncate();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Exception $e) {}

        return redirect()->route('admin.dashboard')->with('success', 'Seluruh database telah di-reset (dikosongkan 100%)!');
    }

    private function ensureHeritageColumnsExist(): void
    {
        try {
            if (Schema::hasTable('heritages')) {
                if (!Schema::hasColumn('heritages', 'additional_sections')) {
                    Schema::table('heritages', function (Blueprint $table) {
                        $table->json('additional_sections')->nullable();
                    });
                }
                if (!Schema::hasColumn('heritages', 'source_name')) {
                    Schema::table('heritages', function (Blueprint $table) {
                        $table->string('source_name')->nullable();
                        $table->string('source_url')->nullable();
                    });
                }
                if (!Schema::hasColumn('heritages', 'sources')) {
                    Schema::table('heritages', function (Blueprint $table) {
                        $table->json('sources')->nullable();
                    });
                }
                if (!Schema::hasColumn('heritages', 'model_3d_url')) {
                    Schema::table('heritages', function (Blueprint $table) {
                        $table->string('model_3d_url')->default('');
                    });
                }
            }
        } catch (\Exception $e) {}
    }
}
