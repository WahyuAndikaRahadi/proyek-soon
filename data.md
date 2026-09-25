# PANDUAN BUILD APLIKASI BOOKSTORE (USK) — FINAL
Stack: **Laravel 11/12 + Filament 5 (Admin Panel)** + **Blade + daisyUI 5 + Tailwind CSS (User Panel, via CDN)**
---

## 0. ALUR PENGERJAAN
1. Install Laravel + set `.env` + buat database kosong
2. Install Filament 5 + buat 1 akun admin + **set role admin manual**
3. Migration: `categories`, `books`, `orders` (user_id nullable + data guest), `order_items`, `messages`, tambah kolom `role` ke `users`
4. Model + relasi + `FilamentUser` di `User.php`
5. Filament Resource: `CategoryResource`, `BookResource` (full CRUD), `UserResource` (view-only, hanya role customer), `OrderResource` (view-only + relation manager item)
6. Sisi User: routes → controller → view (Bootstrap, guest checkout diizinkan)
7. Kustomisasi tampilan Admin (warna/logo) & polish tampilan User
8. Testing sesuai checklist akhir
9. Push GitHub + laporan

---

## 1. SETUP PROJECT (TERMINAL)

```bash
composer create-project laravel/laravel:^12.0 bookstore
cd bookstore

# edit .env -> set DB_DATABASE, DB_USERNAME, DB_PASSWORD sesuai database lokal kamu

composer require filament/filament:"~5.0"
php artisan filament:install --panels

# buat akun admin pertama
php artisan make:filament-user
# isi name, email, password -> nanti role-nya kita set 'admin' manual di step migration+seeder (section 3)

php artisan serve
```

---

## 2. MIGRATION

```bash
php artisan make:model Category -m
php artisan make:model Book -m
php artisan make:model Order -m
php artisan make:model OrderItem -m
php artisan make:model Message -m
php artisan make:migration add_role_to_users_table --table=users
```

### `xxxx_create_categories_table.php`
```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
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
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->string('title');
        $table->string('author');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->unsignedInteger('stock')->default(0);
        $table->string('cover')->nullable();
        $table->timestamps();
    });
}
```

### `xxxx_create_orders_table.php`  (⚠️ diperbarui: dukung guest, tanpa login)
```php
public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        // nullable -> order boleh dari guest (tidak login)
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

        // dipakai kalau user TIDAK login saat checkout
        $table->string('guest_name')->nullable();
        $table->string('guest_email')->nullable();
        $table->string('guest_phone')->nullable();

        $table->string('order_code')->unique();
        $table->decimal('total_price', 12, 2);
        $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
        $table->string('payment_method')->default('cod'); // Payment at Delivery
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
        $table->string('book_title'); // snapshot judul (jaga-jaga kalau buku dihapus nanti)
        $table->unsignedInteger('qty');
        $table->decimal('price', 10, 2); // snapshot harga saat dibeli
        $table->timestamps();
    });
}
```

### `xxxx_create_messages_table.php` (Contact to Admin — tetap perlu login, wajar krn butuh balasan personal)
```php
public function up(): void
{
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('subject');
        $table->text('body');
        $table->boolean('is_read')->default(false);
        $table->timestamps();
    });
}
```

### `xxxx_add_role_to_users_table.php` (⚠️ fix bug "tidak memiliki role")
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // 'admin' = bisa masuk /admin, 'customer' = user biasa (default)
        $table->string('role')->default('customer')->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

Jalankan:
```bash
php artisan migrate
```

Set akun admin yang tadi dibuat jadi role `admin` (lewat tinker):
```bash
php artisan tinker
>>> App\Models\User::where('email', 'admin@admin.com')->update(['role' => 'admin']);
>>> exit
```
(ganti `admin@admin.com` sesuai email yang kamu isi di `make:filament-user`)

---

## 3. MODEL & RELASI

### `app/Models/Category.php`
```php
class Category extends Model
{
    protected $guarded = [];

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
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

### `app/Models/Order.php`  (⚠️ tambah accessor nama & email customer, support guest)
```php
class Order extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // dipakai di tabel admin & view: kalau login -> nama akun, kalau guest -> nama yang diisi manual
    public function getCustomerNameAttribute(): string
    {
        return $this->user->name ?? $this->guest_name ?? '-';
    }

    public function getCustomerEmailAttribute(): string
    {
        return $this->user->email ?? $this->guest_email ?? '-';
    }
}
```

### `app/Models/OrderItem.php`
```php
class OrderItem extends Model
{
    protected $guarded = [];

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
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### `app/Models/User.php`  (⚠️ tambah role + akses panel Filament)
```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser
{
    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];

    // ini kunci fix bug "tidak punya role": hanya role admin yang boleh masuk /admin
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
```

---

## 4. FILAMENT RESOURCE (ADMIN) — sintaks Filament v5

```bash
php artisan make:filament-resource Category --generate
php artisan make:filament-resource Book --generate
php artisan make:filament-resource User --generate --view
php artisan make:filament-resource Order --generate --view
php artisan make:filament-relation-manager OrderResource items book_title
```

### `CategoryResource` — full CRUD (Add/Update/Delete Kategori)
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
            ->live(onBlur: true)
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
        ->recordActions([EditAction::make(), DeleteAction::make()]);
}
```

### `BookResource` — full CRUD (Add/Update/Delete Data Buku)
```php
use Filament\Schemas\Schema;
use Filament\Forms\Components\{Select, TextInput, Textarea, FileUpload};
use Filament\Tables\Table;
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Actions\{EditAction, DeleteAction};

public static function form(Schema $schema): Schema
{
    return $schema->components([
        Select::make('category_id')->relationship('category', 'name')->required()->searchable(),
        TextInput::make('title')->required(),
        TextInput::make('author')->required(),
        Textarea::make('description')->columnSpanFull(),
        TextInput::make('price')->numeric()->prefix('Rp')->required(),
        TextInput::make('stock')->numeric()->required(),
        FileUpload::make('cover')->image()->directory('covers')->columnSpanFull(),
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
        ->recordActions([EditAction::make(), DeleteAction::make()]);
}
```

### `UserResource` — VIEW-ONLY, hanya tampilkan role `customer` (List User yang terdaftar)
```php
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

// hanya tampilkan customer, akun admin tidak usah dicampur di sini
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->where('role', 'customer');
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('orders_count')->counts('orders')->label('Total Pesanan'),
            TextColumn::make('created_at')->date()->label('Terdaftar Sejak'),
        ])
        ->recordActions([ViewAction::make()]); // cuma bisa lihat detail, tidak bisa create/edit/delete
}

// hapus tombol create sepenuhnya
public static function canCreate(): bool
{
    return false;
}

// PENTING: hapus juga route create & edit di getPages() (lihat di bawah)
// supaya tidak forbidden saat diakses manual lewat URL
public static function getPages(): array
{
    return [
        'index' => Pages\ListUsers::route('/'),
        'view'  => Pages\ViewUser::route('/{record}'),
    ];
}
```

### `OrderResource` — VIEW-ONLY (List pesanan dari user berbeda-beda), status masih bisa diubah admin
```php
use Filament\Tables\Table;
use Filament\Tables\Columns\{TextColumn, SelectColumn};
use Filament\Actions\ViewAction;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('order_code')->label('Kode Pesanan')->searchable(),
            TextColumn::make('customer_name')->label('Pemesan'), // pakai accessor dari Order model
            TextColumn::make('customer_email')->label('Email'),
            TextColumn::make('total_price')->money('IDR')->label('Total'),
            SelectColumn::make('status') // ini SATU-SATUNYA yang boleh diubah admin
                ->options(['pending' => 'Pending', 'paid' => 'Lunas', 'cancelled' => 'Batal']),
            TextColumn::make('payment_method')->badge()->label('Pembayaran'),
            TextColumn::make('created_at')->dateTime()->label('Tanggal'),
        ])
        ->recordActions([ViewAction::make()]); // cuma lihat detail, tidak ada edit/delete manual
}

// TIDAK ADA create/edit sama sekali -> ini fix utama bug forbidden
public static function canCreate(): bool
{
    return false;
}

public static function getPages(): array
{
    return [
        'index' => Pages\ListOrders::route('/'),
        'view'  => Pages\ViewOrder::route('/{record}'),
    ];
}
```

### `app/Filament/Resources/Orders/RelationManagers/ItemsRelationManager.php`
Ini yang menampilkan **daftar buku dalam 1 pesanan** — otomatis read-only karena ditaruh di halaman View (Filament v5 otomatis matikan create/edit/delete relation manager di halaman View, jadi tidak perlu setting tambahan).
```php
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('book_title')
            ->columns([
                TextColumn::make('book_title')->label('Judul Buku'),
                TextColumn::make('qty')->label('Jumlah'),
                TextColumn::make('price')->money('IDR')->label('Harga Satuan'),
            ]);
        // tidak perlu ->headerActions([CreateAction::make()]) -> otomatis tidak muncul di halaman View
    }
}
```
Daftarkan relation manager ini di `OrderResource.php`:
```php
public static function getRelations(): array
{
    return [
        RelationManagers\ItemsRelationManager::class,
    ];
}
```

---

## 5. SISI USER — CONTROLLER & ROUTES (GUEST CHECKOUT DIIZINKAN)

### Buat controller
```bash
php artisan make:controller HomeController
php artisan make:controller AuthController
php artisan make:controller BookController
php artisan make:controller CartController
php artisan make:controller CheckoutController
php artisan make:controller MessageController
```

### `routes/web.php`  (⚠️ cart & checkout DIPINDAH keluar dari middleware auth)
```php
use App\Http\Controllers\{HomeController, AuthController, BookController, CartController, CheckoutController, MessageController};

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/buku', [BookController::class, 'index'])->name('books.index');
Route::get('/buku/{book}', [BookController::class, 'show'])->name('books.show');

// Auth (opsional, bukan wajib untuk belanja)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Cart & Checkout: BISA DIAKSES GUEST (tanpa login) — cart berbasis session
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{book}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/{book}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Contact to Admin: tetap wajib login (perlu identitas jelas utk dibalas)
Route::middleware('auth')->group(function () {
    Route::post('/contact', [MessageController::class, 'store'])->name('contact.store');
});
Route::get('/contact', [MessageController::class, 'create'])->name('contact.create');
```

### `app/Http/Controllers/HomeController.php`
```php
class HomeController extends Controller
{
    public function index()
    {
        $latestBooks = Book::latest()->take(8)->get();
        return view('home', compact('latestBooks'));
    }

    public function about()
    {
        return view('about');
    }
}
```

### `app/Http/Controllers/AuthController.php`
```php
class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

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
            'role'     => 'customer', // eksplisit -> tidak mungkin nyasar jadi admin
        ]);

        Auth::login($user);
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
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

### `app/Http/Controllers/BookController.php`
```php
class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::query()
            ->when($request->q, fn ($q) => $q->where('title', 'like', "%{$request->q}%")
                ->orWhere('author', 'like', "%{$request->q}%"))
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->paginate(12)->withQueryString();

        $categories = Category::all();
        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }
}
```

### `app/Http/Controllers/CartController.php`  (tetap session-based, tidak butuh login)
```php
class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $books = Book::whereIn('id', array_keys($cart))->get();
        return view('cart.index', compact('books', 'cart'));
    }

    public function add(Book $book)
    {
        $cart = session('cart', []);
        $cart[$book->id] = ($cart[$book->id] ?? 0) + 1;
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

### `app/Http/Controllers/CheckoutController.php`  (⚠️ support guest — bagian paling penting)
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
        // kalau user login -> tidak perlu isi nama/email lagi
        // kalau guest -> wajib isi nama, email, no HP
        $rules = ['shipping_address' => 'required|string'];
        if (! Auth::check()) {
            $rules['guest_name']  = 'required|string|max:255';
            $rules['guest_email'] = 'required|email';
            $rules['guest_phone'] = 'required|string|max:20';
        }
        $data = $request->validate($rules);

        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Keranjang kosong']);
        }

        $books = Book::whereIn('id', array_keys($cart))->get();
        $total = $books->sum(fn ($book) => $book->price * $cart[$book->id]);

        $order = Order::create([
            'user_id'          => Auth::id(), // null kalau guest
            'guest_name'       => $data['guest_name'] ?? null,
            'guest_email'      => $data['guest_email'] ?? null,
            'guest_phone'      => $data['guest_phone'] ?? null,
            'order_code'       => 'INV-' . strtoupper(uniqid()),
            'total_price'      => $total,
            'status'           => 'pending',
            'payment_method'   => 'cod',
            'shipping_address' => $data['shipping_address'],
        ]);

        foreach ($books as $book) {
            OrderItem::create([
                'order_id'   => $order->id,
                'book_id'    => $book->id,
                'book_title' => $book->title, // snapshot judul
                'qty'        => $cart[$book->id],
                'price'      => $book->price,
            ]);
            $book->decrement('stock', $cart[$book->id]);
        }

        session()->forget('cart');
        return redirect()->route('home')->with('success', "Pesanan {$order->order_code} berhasil dibuat! Bayar saat buku sampai (COD).");
    }
}
```

### `app/Http/Controllers/MessageController.php`
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

## 6. TAMPILAN (DaisyUI + Tailwind CSS) — DIDESAIN ULANG, TANPA BOOTSTRAP

> Dicek ke dokumentasi resmi daisyUI 5.x via Context7. Karena tidak ada build step (Vite/npm) yang dijalankan di sini, dipakai **CDN version** daisyUI resmi (`@tailwindcss/browser@4` + `daisyui@5`) — cukup 2 baris `<link>`/`<script>` di `<head>`, tidak perlu `npm install` atau `npm run build`. Tema warna pakai tema bawaan **`emerald`** (hijau, cocok untuk kesan toko buku terpercaya) lewat atribut `data-theme`.

### `resources/views/layouts/app.blade.php` — master layout
```blade
<!DOCTYPE html>
<html lang="id" data-theme="emerald">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BookStore')</title>

    {{-- Tailwind CSS v4 CDN (tanpa build step) --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    {{-- daisyUI 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
</head>
<body class="min-h-screen flex flex-col bg-base-200">

    {{-- NAVBAR --}}
    <div class="navbar bg-base-100 shadow-sm sticky top-0 z-50 px-4 lg:px-8">
        <div class="navbar-start">
            <a href="{{ route('home') }}" class="btn btn-ghost text-xl">📚 BookStore</a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-1">
                <li><a href="{{ route('books.index') }}">Buku</a></li>
                <li><a href="{{ route('about') }}">About Us</a></li>
                <li><a href="{{ route('contact.create') }}">Contact</a></li>
            </ul>
        </div>
        <div class="navbar-end gap-2">
            <a href="{{ route('cart.index') }}" class="btn btn-ghost btn-circle">
                <div class="indicator">
                    🛒
                    @if (($count = collect(session('cart', []))->sum()) > 0)
                        <span class="badge badge-sm badge-primary indicator-item">{{ $count }}</span>
                    @endif
                </div>
            </a>
            @auth
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost">{{ auth()->user()->name }}</div>
                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 mt-3 w-40 p-2 shadow">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="w-full text-left">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    <div class="container mx-auto px-4 mt-4">
        @if (session('success'))
            <div class="alert alert-success mb-4">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- KONTEN --}}
    <main class="container mx-auto px-4 pb-16 flex-1">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="footer sm:footer-horizontal bg-neutral text-neutral-content p-10 mt-auto">
        <aside>
            <p class="font-bold text-lg">📚 BookStore</p>
            <p>Baca lebih mudah, harga lebih bersahabat.<br>&copy; {{ date('Y') }} BookStore.</p>
        </aside>
        <nav>
            <h6 class="footer-title">Navigasi</h6>
            <a href="{{ route('books.index') }}" class="link link-hover">Buku</a>
            <a href="{{ route('about') }}" class="link link-hover">About Us</a>
            <a href="{{ route('contact.create') }}" class="link link-hover">Contact</a>
        </nav>
    </footer>
</body>
</html>
```

### `resources/views/home.blade.php`
```blade
@extends('layouts.app')
@section('content')
    {{-- HERO --}}
    <div class="hero bg-primary text-primary-content rounded-box my-8">
        <div class="hero-content text-center py-16">
            <div class="max-w-md">
                <h1 class="text-4xl font-bold">Temukan Buku Favoritmu</h1>
                <p class="py-4">Ribuan judul, harga terjangkau. Bayar saat buku sampai (COD) — tanpa perlu daftar akun.</p>
                <a href="{{ route('books.index') }}" class="btn btn-neutral">Jelajahi Buku</a>
            </div>
        </div>
    </div>

    <h4 class="text-2xl font-bold mb-4 border-l-4 border-primary pl-3">Buku Terbaru</h4>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @forelse ($latestBooks as $book)
            <div class="card bg-base-100 shadow-sm hover:shadow-xl transition-shadow">
                <figure class="h-52">
                    <img src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://placehold.co/300x400?text=No+Cover' }}" class="object-cover h-full w-full">
                </figure>
                <div class="card-body p-4">
                    <h2 class="card-title text-base">{{ $book->title }}</h2>
                    <p class="text-sm opacity-70">{{ $book->author }}</p>
                    <p class="text-primary font-bold">Rp{{ number_format($book->price, 0, ',', '.') }}</p>
                    <div class="card-actions">
                        <a href="{{ route('books.show', $book) }}" class="btn btn-primary btn-sm btn-block">Lihat Detail</a>
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
    <div class="hero bg-base-100 rounded-box my-8 shadow-sm">
        <div class="hero-content py-12">
            <div class="max-w-2xl">
                <h2 class="text-3xl font-bold mb-3">Tentang Kami</h2>
                <p class="mb-3">
                    BookStore adalah platform jual-beli buku online yang menyediakan berbagai koleksi
                    dari berbagai kategori dengan harga terjangkau. Kami melayani pembayaran secara
                    <strong>Cash on Delivery (COD)</strong> — bayar saat buku sampai di tanganmu, dan kamu
                    bisa langsung checkout tanpa perlu bikin akun dulu.
                </p>
                <p>Dibuat sebagai proyek demonstrasi Junior Web Developer menggunakan Laravel, Filament, dan daisyUI.</p>
            </div>
        </div>
    </div>
@endsection
```

### `resources/views/contact.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <h4 class="text-2xl font-bold mb-4 border-l-4 border-primary pl-3">Hubungi Admin</h4>

    @auth
        <form action="{{ route('contact.store') }}" method="POST" class="card bg-base-100 shadow-sm p-6 max-w-lg">
            @csrf
            <fieldset class="fieldset">
                <legend class="fieldset-legend">Subjek</legend>
                <input type="text" name="subject" class="input w-full" value="{{ old('subject') }}">

                <legend class="fieldset-legend mt-3">Pesan</legend>
                <textarea name="body" rows="5" class="textarea w-full">{{ old('body') }}</textarea>

                <button class="btn btn-primary mt-4">Kirim Pesan</button>
            </fieldset>
        </form>
    @else
        <div class="alert alert-info max-w-lg">
            <span>Silakan <a href="{{ route('login') }}" class="link link-primary">login</a> dulu untuk mengirim pesan ke admin (supaya balasan bisa dikirim ke email kamu).</span>
        </div>
    @endauth
@endsection
```

### `resources/views/auth/register.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <div class="hero py-10">
        <div class="card bg-base-100 w-full max-w-sm shadow-xl">
            <div class="card-body">
                <h4 class="text-xl font-bold mb-2">Registrasi</h4>
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <fieldset class="fieldset">
                        <label class="label">Nama</label>
                        <input type="text" name="name" class="input w-full" value="{{ old('name') }}">

                        <label class="label mt-2">Email</label>
                        <input type="email" name="email" class="input w-full" value="{{ old('email') }}">

                        <label class="label mt-2">Password</label>
                        <input type="password" name="password" class="input w-full">

                        <label class="label mt-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="input w-full">

                        <button class="btn btn-primary mt-4">Daftar</button>
                    </fieldset>
                </form>
                <p class="text-center text-sm mt-2">Sudah punya akun? <a href="{{ route('login') }}" class="link link-primary">Login</a></p>
            </div>
        </div>
    </div>
@endsection
```

### `resources/views/auth/login.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <div class="hero py-10">
        <div class="card bg-base-100 w-full max-w-sm shadow-xl">
            <div class="card-body">
                <h4 class="text-xl font-bold mb-2">Login</h4>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <fieldset class="fieldset">
                        <label class="label">Email</label>
                        <input type="email" name="email" class="input w-full" value="{{ old('email') }}">

                        <label class="label mt-2">Password</label>
                        <input type="password" name="password" class="input w-full">

                        <button class="btn btn-primary mt-4">Login</button>
                    </fieldset>
                </form>
                <p class="text-center text-sm mt-2">Belum punya akun? <a href="{{ route('register') }}" class="link link-primary">Daftar</a></p>
            </div>
        </div>
    </div>
@endsection
```

### `resources/views/books/index.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <h4 class="text-2xl font-bold mb-4 border-l-4 border-primary pl-3">Katalog Buku</h4>

    <form method="GET" class="flex flex-col md:flex-row gap-2 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau penulis..." class="input flex-1">
        <select name="category" class="select md:w-56">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary">Cari</button>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @forelse ($books as $book)
            <div class="card bg-base-100 shadow-sm hover:shadow-xl transition-shadow">
                <figure class="h-52">
                    <img src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://placehold.co/300x400?text=No+Cover' }}" class="object-cover h-full w-full">
                </figure>
                <div class="card-body p-4">
                    <h2 class="card-title text-base">{{ $book->title }}</h2>
                    <p class="text-sm opacity-70">{{ $book->author }}</p>
                    <p class="text-primary font-bold">Rp{{ number_format($book->price, 0, ',', '.') }}</p>
                    <div class="card-actions gap-2">
                        <a href="{{ route('books.show', $book) }}" class="btn btn-outline btn-sm flex-1">Detail</a>
                        @if ($book->stock > 0)
                            <form action="{{ route('cart.add', $book) }}" method="POST" class="flex-1">
                                @csrf
                                <button class="btn btn-primary btn-sm w-full">+ Cart</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p>Buku tidak ditemukan.</p>
        @endforelse
    </div>
    <div class="mt-6 flex justify-center">{{ $books->links() }}</div>
@endsection
```

### `resources/views/books/show.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <div class="grid md:grid-cols-3 gap-8 my-8">
        <figure class="rounded-box overflow-hidden shadow-sm">
            <img src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://placehold.co/400x550?text=No+Cover' }}" class="w-full object-cover">
        </figure>
        <div class="md:col-span-2">
            <div class="badge badge-outline mb-2">{{ $book->category->name }}</div>
            <h3 class="text-3xl font-bold">{{ $book->title }}</h3>
            <p class="opacity-70 mb-2">Penulis: {{ $book->author }}</p>
            <p class="text-2xl text-primary font-bold mb-2">Rp{{ number_format($book->price, 0, ',', '.') }}</p>
            <div class="badge {{ $book->stock > 0 ? 'badge-success' : 'badge-error' }} mb-4">
                {{ $book->stock > 0 ? "Stok tersedia ({$book->stock})" : 'Stok habis' }}
            </div>
            <p class="mb-6">{{ $book->description }}</p>

            @if ($book->stock > 0)
                <form action="{{ route('cart.add', $book) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary btn-lg">Tambah ke Keranjang</button>
                </form>
            @endif
        </div>
    </div>
@endsection
```

### `resources/views/cart/index.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <h4 class="text-2xl font-bold mb-4 border-l-4 border-primary pl-3">Keranjang Belanja</h4>

    @if ($books->isEmpty())
        <div class="alert alert-info"><span>Keranjang kosong. <a href="{{ route('books.index') }}" class="link link-primary">Cari buku</a> dulu yuk.</span></div>
    @else
        <div class="overflow-x-auto bg-base-100 rounded-box shadow-sm">
            <table class="table">
                <thead>
                    <tr><th>Buku</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th></th></tr>
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
                                    <button class="btn btn-error btn-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex justify-between items-center mt-4">
            <h5 class="text-lg">Total: <span class="text-primary font-bold">Rp{{ number_format($total, 0, ',', '.') }}</span></h5>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg">Checkout →</a>
        </div>
    @endif
@endsection
```

### `resources/views/checkout/index.blade.php`
```blade
@extends('layouts.app')
@section('content')
    <h4 class="text-2xl font-bold mb-4 border-l-4 border-primary pl-3">Checkout</h4>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="card bg-base-100 shadow-sm p-6">
            @php $total = 0; @endphp
            @foreach ($books as $book)
                @php $subtotal = $book->price * $cart[$book->id]; $total += $subtotal; @endphp
                <div class="flex justify-between border-b border-base-200 py-2">
                    <span>{{ $book->title }} x{{ $cart[$book->id] }}</span>
                    <span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="flex justify-between pt-3">
                <strong>Total</strong>
                <strong class="text-primary">Rp{{ number_format($total, 0, ',', '.') }}</strong>
            </div>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" class="card bg-base-100 shadow-sm p-6">
            @csrf
            <fieldset class="fieldset">
                @guest
                    {{-- guest checkout: wajib isi identitas manual --}}
                    <label class="label">Nama Lengkap</label>
                    <input type="text" name="guest_name" class="input w-full" value="{{ old('guest_name') }}">

                    <label class="label mt-2">Email</label>
                    <input type="email" name="guest_email" class="input w-full" value="{{ old('guest_email') }}">

                    <label class="label mt-2">No. HP</label>
                    <input type="text" name="guest_phone" class="input w-full" value="{{ old('guest_phone') }}">

                    <div class="alert alert-info mt-3 text-sm">
                        <span>Sudah punya akun? <a href="{{ route('login') }}" class="link link-primary">Login di sini</a> supaya pesanan tercatat di riwayat kamu (opsional).</span>
                    </div>
                @else
                    <p class="mb-2">Pesan atas nama: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})</p>
                @endguest

                <label class="label mt-2">Alamat Pengiriman</label>
                <textarea name="shipping_address" rows="3" class="textarea w-full">{{ old('shipping_address') }}</textarea>

                <p class="text-sm opacity-70 mt-2">Metode pembayaran: <strong>Cash on Delivery</strong> (bayar saat buku sampai)</p>
                <button class="btn btn-primary btn-lg mt-4">Buat Pesanan</button>
            </fieldset>
        </form>
    </div>
@endsection
```


---

## 7. KUSTOMISASI PANEL FILAMENT (warna, logo, dll)

`app/Providers/Filament/AdminPanelProvider.php`:
```php
use Filament\Panel;
use Filament\Support\Colors\Color;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->brandName('BookStore Admin')
        ->brandLogo(asset('images/logo.png'))   // taruh file di public/images/logo.png
        ->brandLogoHeight('2rem')
        ->favicon(asset('images/favicon.png'))
        ->colors([
            'primary' => Color::Emerald, // senada dengan tema hijau sisi user
            'danger'  => Color::Rose,
            'success' => Color::Green,
            'warning' => Color::Amber,
            'info'    => Color::Blue,
            'gray'    => Color::Slate,
        ])
        ->font('Poppins')
        ->darkMode(isForced: false)
        ->navigationGroups([
            \Filament\Navigation\NavigationGroup::make('Manajemen Toko'),
            \Filament\Navigation\NavigationGroup::make('Data Pengguna'),
        ])
        // ...method bawaan lain (discoverResources, login, dsb) biarkan default dari installer
        ;
}
```

Tambahkan di tiap Resource biar masuk grup yang tepat:
```php
// di CategoryResource.php & BookResource.php
protected static ?string $navigationGroup = 'Manajemen Toko';

// di UserResource.php & OrderResource.php
protected static ?string $navigationGroup = 'Data Pengguna';
```

Icon menu (opsional, ganti sesuai selera — cari nama lain di heroicons.com):
```php
protected static ?string $navigationIcon = 'heroicon-o-book-open';   // BookResource
protected static ?string $navigationIcon = 'heroicon-o-tag';         // CategoryResource
protected static ?string $navigationIcon = 'heroicon-o-users';       // UserResource
protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';// OrderResource
```

Setelah edit provider, jalankan:
```bash
php artisan optimize:clear
php artisan storage:link
```

---

## 8. CHECKLIST TESTING FINAL

- [ ] Login `/admin` **hanya bisa** masuk kalau role user = `admin` (coba login pakai akun customer biasa → harus ditolak)
- [ ] Buka `/admin/orders/create` manual lewat URL → harus **404**, bukan forbidden (karena route create sudah dihapus total)
- [ ] Admin CRUD penuh di Category & Book (tambah/edit/hapus)
- [ ] Admin cuma bisa **lihat** (List + View) di User & Order — tidak ada tombol create/edit/delete di kedua resource ini
- [ ] Di halaman View Order, muncul daftar item buku yang dibeli (relation manager), read-only
- [ ] Admin masih bisa ubah **status** order (pending/paid/cancelled) langsung dari tabel
- [ ] User **tanpa login** bisa: cari buku → add to cart → checkout (isi nama/email/HP manual) → order berhasil dibuat
- [ ] User yang login bisa checkout tanpa isi ulang nama/email (otomatis dari akun)
- [ ] Stok buku berkurang otomatis setelah checkout, keranjang kosong setelah checkout
- [ ] Contact form tetap wajib login
- [ ] Tampilan pakai daisyUI + Tailwind (navbar, hero, card, badge cart) — bukan Bootstrap lagi, dan tema warna `emerald` konsisten di semua halaman

---

## 9. FINALISASI

```bash
git init
git add .
git commit -m "BookStore App - USK"
gh repo create bookstore --public --source=. --push
```

Buat laporan Ms. Word: link GitHub + rancangan/mockup + screenshot tiap halaman (Home, Buku, Detail, Cart, Checkout, Login/Register, Contact, Admin Category/Book/User/Order) → upload ke `https://lspmi.co.id`.

---

**Status:** panduan ini **final & end-to-end** — role user, guest checkout, admin view-only untuk Order/User, dan tampilan sudah diperbaiki semua sesuai catatan bug sebelumnya. Tinggal copy-paste berurutan dari atas ke bawah.
