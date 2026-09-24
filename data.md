# PANDUAN BUILD APLIKASI BOOKSTORE (USK)
Stack: **Laravel 11/12 + Filament 5 (Admin Panel)** + **Blade + Bootstrap 5 CDN (User Panel)**

> Sudah dicek ulang ke dokumentasi resmi Filament 5.x — ada perubahan API penting dibanding v3 (lihat catatan di tiap bagian): `Form` → `Schema`, `schema()` → `components()`, `actions()` → `recordActions()`, `bulkActions()` → `toolbarActions()`, dan class Action (`EditAction`, `DeleteAction`, `BulkActionGroup`, dst) sekarang dari namespace `Filament\Actions`, bukan lagi `Filament\Tables\Actions`.

Kenapa stack ini?
- **Filament** = admin CRUD (kategori, buku, list user, list order) langsung jadi tanpa bikin view manual → paling cepat & "satset" untuk kondisi ujian yang dibatasi waktu.
- **Bootstrap via CDN** (bukan Tailwind/Breeze) = tidak perlu `npm install` / `npm run build`, tinggal `<link>` CDN, cocok untuk demo cepat & minim resiko error build saat ujian.
- Semua native Laravel (Auth manual pakai `Auth` facade) supaya kamu paham & bisa jelasin tiap baris ke penguji (poin no.2 di soal: "berikan komentar deskripsi tiap fungsi").

---

## 0. ALUR PENGERJAAN (URUTAN SAAT UJIAN)

1. Install Laravel baru + set `.env` + `php artisan migrate` (DB kosong dulu, jangan lupa buat DB di phpMyAdmin/psql)
2. Install Filament, buat admin user
3. Buat migration: `categories`, `books`, `orders`, `order_items`, `messages` (contact to admin)
4. Buat Model + relasi
5. Buat Filament Resource: Category, Book, Order (read-only list), User (read-only list)
6. Buat sisi User: routes → controllers → Blade+Bootstrap views (Home, About, Contact, Book list/search, Detail, Cart, Checkout, Register/Login)
7. Testing manual tiap fitur sesuai checklist di bagian akhir
8. `git init`, push ke GitHub, tulis laporan Word (link repo + screenshot), upload ke link yang diminta

---

## 1. SETUP PROJECT (TERMINAL)

```bash
# 1. Buat project laravel baru
composer create-project laravel/laravel:^12.0 bookstore
cd bookstore

# 2. Set koneksi database di .env (edit manual)
# DB_CONNECTION=mysql
# DB_DATABASE=bookstore
# DB_USERNAME=root
# DB_PASSWORD=

# 3. Install Filament v5 (admin panel)
composer require filament/filament:"~5.0"
php artisan filament:install --panels

# 4. Buat akun admin pertama
php artisan make:filament-user
# isi name, email, password saat diminta -> ini login ke /admin

# 5. Jalankan server
php artisan serve
```

Cek: `http://127.0.0.1:8000/admin` harus muncul login Filament.

---

## 2. MIGRATION (DATABASE)

```bash
php artisan make:model Category -m
php artisan make:model Book -m
php artisan make:model Order -m
php artisan make:model OrderItem -m
php artisan make:model Message -m
```

### `database/migrations/xxxx_create_categories_table.php`
```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');           // nama kategori, mis: "Fiksi"
        $table->string('slug')->unique(); // untuk url friendly
        $table->timestamps();
    });
}
```

### `xxxx_create_books_table.php`
```php
public function up(): void
{
    Schema::create('books', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete(); // relasi ke kategori
        $table->string('title');
        $table->string('author');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);   // harga buku
        $table->unsignedInteger('stock')->default(0); // stok buku
        $table->string('cover')->nullable(); // path gambar cover (dari internet/bebas hak cipta)
        $table->timestamps();
    });
}
```

### `xxxx_create_orders_table.php`
```php
public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // siapa yang order
        $table->string('order_code')->unique();     // kode invoice
        $table->decimal('total_price', 12, 2);
        // status: pending -> diproses admin, paid -> lunas COD, cancelled
        $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
        $table->string('payment_method')->default('cod'); // Payment at Delivery (COD)
        $table->text('shipping_address');
        $table->timestamps();
    });
}
```

### `xxxx_create_order_items_table.php`
```php
public function up(): void
{
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        $table->foreignId('book_id')->constrained();
        $table->unsignedInteger('qty');
        $table->decimal('price', 10, 2); // harga saat dibeli (snapshot, bukan harga live)
        $table->timestamps();
    });
}
```

### `xxxx_create_messages_table.php`  (fitur "Contact to Admin")
```php
public function up(): void
{
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('subject');
        $table->text('body');
        $table->boolean('is_read')->default(false); // status dibaca admin
        $table->timestamps();
    });
}
```

Jalankan:
```bash
php artisan migrate
```

---

## 3. MODEL & RELASI

### `app/Models/Category.php`
```php
class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    // satu kategori punya banyak buku
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
```

### `app/Models/Book.php`
```php
class Book extends Model
{
    protected $fillable = ['category_id', 'title', 'author', 'description', 'price', 'stock', 'cover'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

### `app/Models/Order.php`
```php
class Order extends Model
{
    protected $fillable = ['user_id', 'order_code', 'total_price', 'status', 'payment_method', 'shipping_address'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

### `app/Models/OrderItem.php`
```php
class OrderItem extends Model
{
    protected $fillable = ['order_id', 'book_id', 'qty', 'price'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
```

### `app/Models/Message.php`
```php
class Message extends Model
{
    protected $fillable = ['user_id', 'subject', 'body', 'is_read'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

> Jangan lupa tambahkan relasi `orders()` dan `messages()` di `User.php` juga (`hasMany`).

---

## 4. FILAMENT RESOURCE (ADMIN — bagian Admin di soal) — sintaks Filament v5

```bash
php artisan make:filament-resource Category --generate
php artisan make:filament-resource Book --generate
php artisan make:filament-resource Order --generate
php artisan make:filament-resource User --generate
```

> **Beda dari v3 (perhatikan ini!):**
> - Method `form(Form $form)` → sekarang `form(Schema $schema)`, dan `->schema([...])` → `->components([...])`
> - Method table masih `table(Table $table)` tapi `->actions([...])` → `->recordActions([...])`, `->bulkActions([...])` → `->toolbarActions([...])`
> - Class action (`EditAction`, `DeleteAction`, `BulkActionGroup`, `DeleteBulkAction`) di-import dari `Filament\Actions`, **bukan** `Filament\Tables\Actions` lagi
> - Field form (`TextInput`, `Select`, `Textarea`, `FileUpload`) tetap dari `Filament\Forms\Components` (tidak berubah)

### `app/Filament/Resources/CategoryResource.php` (form + table)
```php
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;

public static function form(Schema $schema): Schema
{
    return $schema->components([
        TextInput::make('name')
            ->required()
            ->live(onBlur: true) // auto generate slug saat pindah field
            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
        TextInput::make('slug')->required()->unique(ignoreRecord: true),
    ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('slug'),
            TextColumn::make('books_count')->counts('books')->label('Jumlah Buku'),
        ])
        ->recordActions([
            EditAction::make(),
            DeleteAction::make(), // Add/Update/Delete kategori
        ]);
}
```

### `app/Filament/Resources/BookResource.php`
```php
use Filament\Schemas\Schema;
use Filament\Forms\Components\{Select, TextInput, Textarea, FileUpload};
use Filament\Tables\Table;
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Actions\{EditAction, DeleteAction};

public static function form(Schema $schema): Schema
{
    return $schema->components([
        Select::make('category_id')->relationship('category', 'name')->required(),
        TextInput::make('title')->required(),
        TextInput::make('author')->required(),
        Textarea::make('description'),
        TextInput::make('price')->numeric()->prefix('Rp')->required(),
        TextInput::make('stock')->numeric()->required(),
        FileUpload::make('cover')->image()->directory('covers'), // upload cover buku
    ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            ImageColumn::make('cover'),
            TextColumn::make('title')->searchable(),
            TextColumn::make('category.name')->label('Kategori'),
            TextColumn::make('price')->money('IDR'),
            TextColumn::make('stock'),
        ])
        ->recordActions([
            EditAction::make(),
            DeleteAction::make(), // Add/Update/Delete data buku
        ]);
}
```

### `UserResource.php` — cukup daftar (List User yang sudah terdaftar), matikan create dari admin
```php
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

public static function table(Table $table): Table
{
    return $table->columns([
        TextColumn::make('name')->searchable(),
        TextColumn::make('email')->searchable(),
        TextColumn::make('created_at')->date()->label('Terdaftar Sejak'),
    ]);
}

// hilangkan tombol create supaya admin tidak bisa bikin user manual dari sini
public static function canCreate(): bool
{
    return false;
}
```

### `OrderResource.php` — List pesanan tiap user, admin bisa ubah status
```php
use Filament\Tables\Table;
use Filament\Tables\Columns\{TextColumn, SelectColumn};

public static function table(Table $table): Table
{
    return $table->columns([
        TextColumn::make('order_code'),
        TextColumn::make('user.name')->label('Pemesan'),
        TextColumn::make('total_price')->money('IDR'),
        SelectColumn::make('status')
            ->options(['pending' => 'Pending', 'paid' => 'Lunas', 'cancelled' => 'Batal']),
        TextColumn::make('created_at')->dateTime(),
    ]);
}
```

### Kalau pakai `--generate`, filenya otomatis dipecah ke folder terpisah
Di v5, `php artisan make:filament-resource Book --generate` akan membuat struktur:
```
app/Filament/Resources/Books/
├── BookResource.php
├── Pages/
│   ├── ListBooks.php
│   ├── CreateBook.php
│   └── EditBook.php
├── Schemas/
│   └── BookForm.php     <- isi form() di sini
└── Tables/
    └── BooksTable.php   <- isi table() di sini
```
`BookResource.php` cukup memanggil:
```php
public static function form(Schema $schema): Schema
{
    return BookForm::configure($schema);
}

public static function table(Table $table): Table
{
    return BooksTable::configure($table);
}
```

---

## 5. SISI USER (Blade + Bootstrap)

### Routes — `routes/web.php`
```php
use App\Http\Controllers\{HomeController, AuthController, BookController, CartController, CheckoutController, MessageController};

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/buku', [BookController::class, 'index'])->name('books.index');   // list + search
Route::get('/buku/{book}', [BookController::class, 'show'])->name('books.show');

// Auth
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Harus login untuk fitur berikut
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{book}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{book}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::post('/contact', [MessageController::class, 'store'])->name('contact.store');
});

Route::get('/contact', [MessageController::class, 'create'])->name('contact.create');
```

### `app/Http/Controllers/AuthController.php`
```php
class AuthController extends Controller
{
    // tampilkan form registrasi
    public function showRegister()
    {
        return view('auth.register');
    }

    // proses simpan user baru + auto login
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user); // langsung login setelah daftar
        return redirect()->route('home')->with('success', 'Registrasi berhasil!');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Auth::attempt cocokkan email+password ke tabel users
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // cegah session fixation
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('home');
    }
}
```

### `app/Http/Controllers/HomeController.php` (Home + About Us)
```php
class HomeController extends Controller
{
    // halaman utama: tampilkan beberapa buku terbaru sebagai highlight
    public function index()
    {
        $latestBooks = Book::latest()->take(8)->get();
        return view('home', compact('latestBooks'));
    }

    // halaman "About Us"
    public function about()
    {
        return view('about');
    }
}
```

### `app/Http/Controllers/BookController.php` (List + Pencarian buku)
```php
class BookController extends Controller
{
    public function index(Request $request)
    {
        // pencarian by judul/penulis, filter kategori (opsional)
        $books = Book::query()
            ->when($request->q, fn ($q) => $q->where('title', 'like', "%{$request->q}%")
                ->orWhere('author', 'like', "%{$request->q}%"))
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->paginate(12);

        $categories = Category::all();
        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }
}
```

### `app/Http/Controllers/CartController.php` (Cart pakai session, tanpa tabel DB — lebih cepat untuk demo)
```php
class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []); // ['book_id' => qty]
        $books = Book::whereIn('id', array_keys($cart))->get();
        return view('cart.index', compact('books', 'cart'));
    }

    // tambah buku ke keranjang
    public function add(Book $book)
    {
        $cart = session('cart', []);
        $cart[$book->id] = ($cart[$book->id] ?? 0) + 1; // tambah qty jika sudah ada
        session(['cart' => $cart]);
        return back()->with('success', 'Buku ditambahkan ke keranjang');
    }

    public function remove(Book $book)
    {
        $cart = session('cart', []);
        unset($cart[$book->id]);
        session(['cart' => $cart]);
        return back();
    }
}
```

### `app/Http/Controllers/CheckoutController.php` (Payment at Delivery)
```php
class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $books = Book::whereIn('id', array_keys($cart))->get();
        return view('checkout.index', compact('books', 'cart'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['shipping_address' => 'required|string']);
        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Keranjang kosong']);
        }

        $books = Book::whereIn('id', array_keys($cart))->get();
        $total = $books->sum(fn ($book) => $book->price * $cart[$book->id]);

        // buat order dengan status pending, payment_method = cod (bayar saat barang sampai)
        $order = Order::create([
            'user_id'          => Auth::id(),
            'order_code'       => 'INV-' . strtoupper(uniqid()),
            'total_price'      => $total,
            'status'           => 'pending',
            'payment_method'   => 'cod',
            'shipping_address' => $data['shipping_address'],
        ]);

        foreach ($books as $book) {
            OrderItem::create([
                'order_id' => $order->id,
                'book_id'  => $book->id,
                'qty'      => $cart[$book->id],
                'price'    => $book->price, // snapshot harga saat itu
            ]);
            $book->decrement('stock', $cart[$book->id]); // kurangi stok
        }

        session()->forget('cart'); // kosongkan keranjang
        return redirect()->route('home')->with('success', "Pesanan {$order->order_code} berhasil dibuat! Bayar saat barang sampai.");
    }
}
```

### `app/Http/Controllers/MessageController.php` (Contact to Admin)
```php
class MessageController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        Message::create([
            'user_id' => Auth::id(),
            'subject' => $data['subject'],
            'body'    => $data['body'],
        ]);

        return back()->with('success', 'Pesan terkirim ke admin');
    }
}
```

---

## 6. BLADE + BOOTSTRAP VIEWS

### `resources/views/layouts/app.blade.php` (master layout, Bootstrap via CDN)
```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BookStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">BookStore</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('books.index') }}">Buku</a>
                <a class="nav-link" href="{{ route('about') }}">About Us</a>
                <a class="nav-link" href="{{ route('contact.create') }}">Contact</a>
                @auth
                    <a class="nav-link" href="{{ route('cart.index') }}">Keranjang</a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-link nav-link">Logout ({{ auth()->user()->name }})</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### `resources/views/books/index.blade.php` (List + Search + Add to Cart)
```blade
@extends('layouts.app')
@section('content')
    <form method="GET" class="row mb-4">
        <div class="col-md-8">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul/penulis...">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1"><button class="btn btn-primary">Cari</button></div>
    </form>

    <div class="row">
        @foreach ($books as $book)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://via.placeholder.com/300x400?text=No+Cover' }}" class="card-img-top">
                    <div class="card-body">
                        <h6>{{ $book->title }}</h6>
                        <p class="text-muted mb-1">{{ $book->author }}</p>
                        <p class="fw-bold">Rp{{ number_format($book->price, 0, ',', '.') }}</p>
                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        @auth
                            <form action="{{ route('cart.add', $book) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-primary">+ Cart</button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{ $books->links() }}
@endsection
```

### `resources/views/home.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <div class="p-5 mb-4 bg-light rounded-3">
        <h1>Selamat Datang di BookStore</h1>
        <p class="lead">Temukan buku favoritmu dengan harga terbaik. Bayar saat barang sampai (COD).</p>
        <a href="{{ route('books.index') }}" class="btn btn-primary">Lihat Semua Buku</a>
    </div>

    <h4 class="mb-3">Buku Terbaru</h4>
    <div class="row">
        @forelse ($latestBooks as $book)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://via.placeholder.com/300x400?text=No+Cover' }}" class="card-img-top">
                    <div class="card-body">
                        <h6>{{ $book->title }}</h6>
                        <p class="text-muted mb-1">{{ $book->author }}</p>
                        <p class="fw-bold">Rp{{ number_format($book->price, 0, ',', '.') }}</p>
                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <p>Belum ada buku. Tambahkan lewat halaman admin di <code>/admin</code>.</p>
        @endforelse
    </div>
@endsection
```

### `resources/views/about.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <h2>Tentang Kami</h2>
    <p>
        BookStore adalah platform jual-beli buku online yang menyediakan berbagai koleksi
        buku dari berbagai kategori dengan harga terjangkau. Kami melayani pembayaran
        secara Cash on Delivery (COD) — bayar saat buku sampai di tanganmu.
    </p>
    <p>
        Dibuat sebagai proyek demonstrasi Junior Web Developer menggunakan Laravel,
        Filament (panel admin), dan Bootstrap (tampilan user).
    </p>
@endsection
```

### `resources/views/contact.blade.php` (Contact to Admin)
```blade
@extends('layouts.app')
@section('content')
    <h2>Hubungi Admin</h2>

    @auth
        <form action="{{ route('contact.store') }}" method="POST" class="col-md-6">
            @csrf
            <div class="mb-3">
                <label class="form-label">Subjek</label>
                <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}">
                @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Pesan</label>
                <textarea name="body" rows="5" class="form-control @error('body') is-invalid @enderror">{{ old('body') }}</textarea>
                @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <button class="btn btn-primary">Kirim Pesan</button>
        </form>
    @else
        <p>Silakan <a href="{{ route('login') }}">login</a> dulu untuk mengirim pesan ke admin.</p>
    @endauth
@endsection
```

### `resources/views/auth/register.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <h2>Registrasi</h2>
    <form action="{{ route('register') }}" method="POST" class="col-md-6">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <button class="btn btn-primary">Daftar</button>
        <a href="{{ route('login') }}" class="btn btn-link">Sudah punya akun? Login</a>
    </form>
@endsection
```

### `resources/views/auth/login.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <h2>Login</h2>
    <form action="{{ route('login') }}" method="POST" class="col-md-6">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button class="btn btn-primary">Login</button>
        <a href="{{ route('register') }}" class="btn btn-link">Belum punya akun? Daftar</a>
    </form>
@endsection
```

### `resources/views/books/show.blade.php` (Detail Buku)
```blade
@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-4">
            <img src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://via.placeholder.com/400x550?text=No+Cover' }}" class="img-fluid rounded">
        </div>
        <div class="col-md-8">
            <h3>{{ $book->title }}</h3>
            <p class="text-muted">Penulis: {{ $book->author }} | Kategori: {{ $book->category->name }}</p>
            <h4 class="text-primary">Rp{{ number_format($book->price, 0, ',', '.') }}</h4>
            <p>Stok: {{ $book->stock }}</p>
            <p>{{ $book->description }}</p>

            @auth
                @if ($book->stock > 0)
                    <form action="{{ route('cart.add', $book) }}" method="POST">
                        @csrf
                        <button class="btn btn-primary">Tambah ke Keranjang</button>
                    </form>
                @else
                    <span class="badge bg-danger">Stok Habis</span>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary">Login untuk membeli</a>
            @endauth
        </div>
    </div>
@endsection
```

### `resources/views/cart/index.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <h2>Keranjang Belanja</h2>

    @if ($books->isEmpty())
        <p>Keranjang kosong. <a href="{{ route('books.index') }}">Cari buku</a> dulu yuk.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Buku</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th></th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($books as $book)
                    @php $subtotal = $book->price * $cart[$book->id]; $total += $subtotal; @endphp
                    <tr>
                        <td>{{ $book->title }}</td>
                        <td>Rp{{ number_format($book->price, 0, ',', '.') }}</td>
                        <td>{{ $cart[$book->id] }}</td>
                        <td>Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $book) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <h5>Total: Rp{{ number_format($total, 0, ',', '.') }}</h5>
        <a href="{{ route('checkout.index') }}" class="btn btn-success">Checkout</a>
    @endif
@endsection
```

### `resources/views/checkout/index.blade.php` (Payment at Delivery)
```blade
@extends('layouts.app')
@section('content')
    <h2>Checkout</h2>

    <table class="table">
        @php $total = 0; @endphp
        @foreach ($books as $book)
            @php $subtotal = $book->price * $cart[$book->id]; $total += $subtotal; @endphp
            <tr>
                <td>{{ $book->title }} x {{ $cart[$book->id] }}</td>
                <td>Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
    <h5>Total: Rp{{ number_format($total, 0, ',', '.') }}</h5>

    <form action="{{ route('checkout.store') }}" method="POST" class="col-md-6">
        @csrf
        <div class="mb-3">
            <label class="form-label">Alamat Pengiriman</label>
            <textarea name="shipping_address" rows="3" class="form-control @error('shipping_address') is-invalid @enderror">{{ old('shipping_address') }}</textarea>
            @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <p><strong>Metode Pembayaran:</strong> Cash on Delivery (bayar saat buku sampai)</p>
        <button class="btn btn-success">Buat Pesanan</button>
    </form>
@endsection
```

> Semua view di atas sudah lengkap, tinggal copy-paste ke path masing-masing (bikin foldernya dulu kalau belum ada, mis. `resources/views/auth/`, `resources/views/books/`, `resources/views/cart/`, `resources/views/checkout/`).

---

## 7. FILE UPLOAD SUPAYA COVER BUKU MUNCUL
```bash
php artisan storage:link
```

---

## 8. CHECKLIST TESTING SEBELUM SUBMIT

- [ ] Admin bisa login ke `/admin`, tambah/edit/hapus kategori & buku
- [ ] Admin bisa lihat list user terdaftar
- [ ] Admin bisa lihat & ubah status pesanan tiap user
- [ ] User bisa register & login
- [ ] Halaman About Us tampil
- [ ] Contact form terkirim (tersimpan di tabel `messages`)
- [ ] Search buku by judul/penulis/kategori jalan
- [ ] Add to Cart & lihat isi keranjang
- [ ] Checkout membuat order dengan `payment_method = cod`, stok berkurang, keranjang kosong setelah checkout
- [ ] Semua gambar buku dari sumber bebas hak cipta (unsplash/pexels/pixabay)

---

## 9. FINALISASI (Poin 5 di Soal)

```bash
git init
git add .
git commit -m "BookStore App - USK"
gh repo create bookstore --public --source=. --push
# atau manual: buat repo di github.com lalu git remote add origin <url> && git push -u origin main
```

Lalu: buat laporan di Ms. Word berisi link GitHub, rancangan/mockup (boleh sketsa sederhana alur Admin & User), dan screenshot tiap halaman → upload ke `https://lspmi.co.id`.

---

**Catatan:** panduan ini sudah cukup untuk direplikasi ulang dari nol saat ujian (semua perintah terminal + kode inti ada). Kalau mau saya lengkapi view yang belum full (cart, checkout, login, register, contact) tinggal bilang, nanti saya buatkan filenya juga.
