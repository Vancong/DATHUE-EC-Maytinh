@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Sản Phẩm – TechStore Admin')
@section('header_title', 'Chỉnh Sửa Sản Phẩm: ' . $product->name)

@section('content')
<div class="max-w-4xl bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">

    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900">Cập Nhật Thông Tin Sản Phẩm</h3>
        <a href="{{ route('admin.products.index') }}" class="text-xs text-slate-500 hover:text-slate-800">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Row 1: Name, SKU -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tên sản phẩm *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">
            </div>

            <div>
                <label for="sku" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mã SKU (Duy nhất) *</label>
                <input
                    type="text"
                    id="sku"
                    name="sku"
                    value="{{ old('sku', $product->sku) }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono uppercase focus:outline-none focus:border-blue-600">
            </div>
        </div>

        <!-- Row 2: Category, Brand, Stock Quantity & Min Stock -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label for="category_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Danh mục *</label>
                <select id="category_id" name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">
                    <option value="">— Chọn danh mục</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="brand_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Thương hiệu</label>
                <select id="brand_id" name="brand_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">
                    <option value="">— Chọn thương hiệu</option>
                    @foreach($brands as $b)
                    <option value="{{ $b->id }}" {{ old('brand_id', $product->brand_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="stock_quantity" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Số lượng tồn kho *</label>
                <input
                    type="number"
                    id="stock_quantity"
                    name="stock_quantity"
                    value="{{ old('stock_quantity', $product->stock_quantity) }}"
                    min="0"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:outline-none focus:border-blue-600">
            </div>

            <div>
                <label for="min_stock" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Ngưỡng báo sắp hết</label>
                <input
                    type="number"
                    id="min_stock"
                    name="min_stock"
                    value="{{ old('min_stock', $product->min_stock) }}"
                    min="0"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:outline-none focus:border-blue-600">
            </div>
        </div>

        <!-- Row 3: Prices -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Giá bán niêm yết (VNĐ) *</label>
                <input
                    type="number"
                    id="price"
                    name="price"
                    value="{{ old('price', $product->price) }}"
                    required
                    min="0"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:outline-none focus:border-blue-600">
            </div>

            <div>
                <label for="sale_price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Giá khuyến mãi (Nếu có)</label>
                <input
                    type="number"
                    id="sale_price"
                    name="sale_price"
                    value="{{ old('sale_price', $product->sale_price) }}"
                    min="0"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-rose-600 focus:outline-none focus:border-blue-600">
            </div>

            <div>
                <label for="cost_price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Giá vốn nhập</label>
                <input
                    type="number"
                    id="cost_price"
                    name="cost_price"
                    value="{{ old('cost_price', $product->cost_price) }}"
                    min="0"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">
            </div>
        </div>

        <!-- Thumbnail & Short Description -->
        <div>
            <label for="thumbnail" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">URL Ảnh Đại Diện (Thumbnail)</label>
            <input
                type="text"
                id="thumbnail"
                name="thumbnail"
                value="{{ old('thumbnail', $product->thumbnail) }}"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">
        </div>

        <div>
            <label for="short_description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mô tả tóm tắt nổi bật</label>
            <textarea
                id="short_description"
                name="short_description"
                rows="2"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">{{ old('short_description', $product->short_description) }}</textarea>
        </div>

        <!-- Specifications Dynamic Repeater -->
        <div class="pt-4 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Thông Số Kỹ Thuật (Specs)</h4>
                    <p class="text-[11px] text-slate-400">Chỉnh sửa hoặc thêm các thông số kỹ thuật</p>
                </div>
                <button type="button" onclick="addSpecRow()" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                    + Thêm dòng thông số
                </button>
            </div>

            <div id="specs-container" class="space-y-2">
                @forelse($product->specifications as $index => $spec)
                <div class="grid grid-cols-12 gap-2 spec-row">
                    <input type="text" name="specs[{{ $index }}][key]" value="{{ $spec->spec_key }}" placeholder="Tên thông số" class="col-span-4 px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                    <input type="text" name="specs[{{ $index }}][value]" value="{{ $spec->spec_value }}" placeholder="Giá trị chi tiết" class="col-span-7 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <button type="button" onclick="this.parentElement.remove()" class="col-span-1 text-slate-400 hover:text-rose-600 text-sm font-bold text-center">✕</button>
                </div>
                @empty
                <div class="grid grid-cols-12 gap-2 spec-row">
                    <input type="text" name="specs[0][key]" value="CPU" placeholder="Tên thông số" class="col-span-4 px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                    <input type="text" name="specs[0][value]" value="" placeholder="Giá trị chi tiết" class="col-span-7 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <button type="button" onclick="this.parentElement.remove()" class="col-span-1 text-slate-400 hover:text-rose-600 text-sm font-bold text-center">✕</button>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Gallery Images Repeater -->
        <div class="pt-4 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Bộ Sưu Tập Ảnh Phụ (Gallery)</h4>
                    <p class="text-[11px] text-slate-400">Danh sách URL ảnh phụ</p>
                </div>
                <button type="button" onclick="addGalleryRow()" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                    + Thêm URL ảnh phụ
                </button>
            </div>

            <div id="gallery-container" class="space-y-2">
                @forelse($product->images as $img)
                <div class="flex items-center gap-2 gallery-row">
                    <input type="text" name="gallery[]" value="{{ $img->image_path }}" placeholder="https://..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <button type="button" onclick="this.parentElement.remove()" class="px-2 text-slate-400 hover:text-rose-600 font-bold">✕</button>
                </div>
                @empty
                <div class="flex items-center gap-2 gallery-row">
                    <input type="text" name="gallery[]" placeholder="https://..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <button type="button" onclick="this.parentElement.remove()" class="px-2 text-slate-400 hover:text-rose-600 font-bold">✕</button>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Description HTML -->
        <div class="pt-4 border-t border-slate-100">
            <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mô tả bài viết chi tiết (HTML)</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono focus:outline-none focus:border-blue-600">{{ old('description', $product->description) }}</textarea>
        </div>

        <!-- Checkboxes -->
        <div class="pt-4 border-t border-slate-100 flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Đặt làm sản phẩm Nổi Bật trang chủ</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Mở bán ngay lập tức</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Hủy</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md transition-colors">
                Cập Nhật Sản Phẩm
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    let specIndex = {{ $product->specifications->count() + 1 }};

    function addSpecRow() {
        const container = document.getElementById('specs-container');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 spec-row';
        row.innerHTML = `
            <input type="text" name="specs[${specIndex}][key]" placeholder="Tên thông số" class="col-span-4 px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
            <input type="text" name="specs[${specIndex}][value]" placeholder="Giá trị chi tiết" class="col-span-7 px-3 py-2 rounded-xl border border-slate-300 text-xs">
            <button type="button" onclick="this.parentElement.remove()" class="col-span-1 text-slate-400 hover:text-rose-600 text-sm font-bold text-center">✕</button>
        `;
        container.appendChild(row);
        specIndex++;
    }

    function addGalleryRow() {
        const container = document.getElementById('gallery-container');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 gallery-row';
        row.innerHTML = `
            <input type="text" name="gallery[]" placeholder="https://..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
            <button type="button" onclick="this.parentElement.remove()" class="px-2 text-slate-400 hover:text-rose-600 font-bold">✕</button>
        `;
        container.appendChild(row);
    }
</script>
@endpush
@endsection