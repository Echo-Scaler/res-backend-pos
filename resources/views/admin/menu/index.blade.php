@extends('admin.layouts.app')

@section('title', 'Menu & Product Management')

@push('styles')
<style>
    .menu-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .menu-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-create-category {
        background: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        padding: 0.65rem 1.15rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-create-category:hover {
        background: var(--bg-body);
        border-color: var(--primary);
    }

    .btn-create-product {
        background: linear-gradient(135deg, var(--primary), #ea580c);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.28);
        transition: all 0.2s ease;
    }

    .btn-create-product:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(var(--primary-rgb), 0.38);
    }

    /* Stats Grid */
    .menu-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .menu-stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .menu-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .icon-dishes { background: rgba(var(--primary-rgb), 0.12); color: var(--primary); }
    .icon-available { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    .icon-stockout { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .icon-categories { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }

    /* Category Filter Pills */
    .category-pills-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .cat-pill {
        padding: 0.45rem 1rem;
        border-radius: 9999px;
        font-size: 0.84rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-muted);
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.15s;
    }

    .cat-pill:hover, .cat-pill.active {
        color: var(--primary);
        border-color: var(--primary);
        background: rgba(var(--primary-rgb), 0.08);
    }

    /* Product Grid Cards */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .product-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .product-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.8rem;
    }

    .product-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        background: var(--bg-body);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }

    .product-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.3;
    }

    .product-code {
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
    }

    .product-price {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--primary);
        margin-top: 0.75rem;
    }

    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.2rem;
        padding-top: 0.8rem;
        border-top: 1px solid var(--border-color);
    }

    .btn-toggle-stock {
        background: none;
        border: none;
        padding: 0.35rem 0.7rem;
        border-radius: 20px;
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .btn-toggle-stock.available {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .btn-toggle-stock.out {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    /* Modal Backdrop & Card */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.65);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        width: 100%;
        max-width: 520px;
        padding: 1.75rem;
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }

    .modal-header h3 {
        font-size: 1.25rem;
        font-weight: 800;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
    }

    .form-control {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-body);
        color: var(--text-main);
        font-size: 0.9rem;
        outline: none;
    }

    .form-control:focus {
        border-color: var(--primary);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="menu-workspace-wrapper">

    <!-- Header -->
    <div class="menu-header">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
                <i class="ti ti-salad" style="color: var(--primary);"></i>
                Menu & Products
            </h1>
        </div>
        <div class="menu-header-actions">
            <button type="button" class="btn-create-category" onclick="openModal('categoryModal')">
                <i class="ti ti-folder-plus"></i>
                <span>+ Add Category</span>
            </button>
            <button type="button" class="btn-create-product" onclick="openModal('productModal')">
                <i class="ti ti-plus"></i>
                <span>+ Add Dish / Product</span>
            </button>
        </div>
    </div>

    <!-- Live Metrics -->
    <div class="menu-stats-grid">
        <div class="menu-stat-card">
            <div class="menu-stat-icon icon-dishes"><i class="ti ti-tools-kitchen-2"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Total Dishes</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ $stats['total_products'] }}</div>
            </div>
        </div>
        <div class="menu-stat-card">
            <div class="menu-stat-icon icon-available"><i class="ti ti-circle-check"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Available for POS</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ $stats['available_products'] }}</div>
            </div>
        </div>
        <div class="menu-stat-card">
            <div class="menu-stat-icon icon-stockout"><i class="ti ti-alert-triangle"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Out of Stock (86)</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ $stats['out_of_stock'] }}</div>
            </div>
        </div>
        <div class="menu-stat-card">
            <div class="menu-stat-icon icon-categories"><i class="ti ti-category"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Categories</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ $stats['total_categories'] }}</div>
            </div>
        </div>
    </div>

    <!-- Category Filter Bar -->
    <div class="category-pills-bar">
        <a href="{{ route('admin.menu.index') }}" class="cat-pill {{ empty($selectedCategory) ? 'active' : '' }}">
            <span>All Menu Items</span>
            <span style="opacity: 0.7;">({{ $stats['total_products'] }})</span>
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('admin.menu.index', ['category_id' => $cat->id]) }}" class="cat-pill {{ $selectedCategory == $cat->id ? 'active' : '' }}">
                <span>{{ $cat->name }}</span>
                <span style="opacity: 0.7;">({{ $cat->products_count }})</span>
            </a>
        @endforeach
    </div>

    <!-- Products Grid -->
    @if($products->isEmpty())
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; text-align: center; padding: 3rem 1.5rem;">
            <i class="ti ti-soup-off" style="font-size: 3rem; color: var(--text-muted); opacity: 0.5;"></i>
            <h3 style="margin-top: 1rem; font-weight: 700;">No menu products found</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.35rem;">Click "+ Add Dish / Product" above to populate your restaurant menu catalogue.</p>
        </div>
    @else
        <div class="products-grid">
            @foreach($products as $product)
                <div class="product-card">
                    <div>
                        <div class="product-card-top">
                            <span class="product-badge">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                            @if($product->code)
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">#{{ $product->code }}</span>
                            @endif
                        </div>
                        <div class="product-title">{{ $product->name }}</div>
                        @if($product->description)
                            <p style="color: var(--text-muted); font-size: 0.82rem; margin-top: 0.35rem; line-height: 1.4;">{{ Str::limit($product->description, 70) }}</p>
                        @endif
                        <div class="product-price">{{ number_format($product->price, 0) }} MMK</div>
                    </div>
                    <div class="product-footer">
                        <form action="{{ route('admin.menu.products.toggle', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-toggle-stock {{ $product->is_available ? 'available' : 'out' }}">
                                <i class="ti {{ $product->is_available ? 'ti-check' : 'ti-ban' }}"></i>
                                <span>{{ $product->is_available ? 'Available' : '86 Out of Stock' }}</span>
                            </button>
                        </form>

                        <form action="{{ route('admin.menu.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete dish {{ addslashes($product->name) }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1.1rem;" title="Delete Product">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $products->links() }}
        </div>
    @endif

</div>

<!-- MODAL: ADD CATEGORY -->
<div id="categoryModal" class="modal-backdrop">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Add Food Category</h3>
            <button type="button" style="background:none; border:none; font-size:1.25rem; cursor:pointer;" onclick="closeModal('categoryModal')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('admin.menu.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Category Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Starters, Hot Kitchen, Drinks" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Brief category notes..."></textarea>
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="0" min="0">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-create-category" onclick="closeModal('categoryModal')">Cancel</button>
                <button type="submit" class="btn-create-product">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: ADD DISH / PRODUCT -->
<div id="productModal" class="modal-backdrop">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Add Dish / Product</h3>
            <button type="button" style="background:none; border:none; font-size:1.25rem; cursor:pointer;" onclick="closeModal('productModal')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('admin.menu.products.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Menu Category</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Select Category (Optional) --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Dish Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Mohinga Traditional Set" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Menu Code / SKU</label>
                    <input type="text" name="code" class="form-control" placeholder="DISH-001">
                </div>
                <div class="form-group">
                    <label>Prep Time (Minutes)</label>
                    <input type="number" name="preparation_time" class="form-control" placeholder="15" min="0">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Selling Price (MMK) *</label>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="6500" required>
                </div>
                <div class="form-group">
                    <label>Cost Price (MMK)</label>
                    <input type="number" step="0.01" name="cost_price" class="form-control" placeholder="3000">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Ingredients, spiciness modifiers..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-create-category" onclick="closeModal('productModal')">Cancel</button>
                <button type="submit" class="btn-create-product">Save Product</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }
    window.onclick = function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.classList.remove('show');
        }
    }
</script>
@endpush
