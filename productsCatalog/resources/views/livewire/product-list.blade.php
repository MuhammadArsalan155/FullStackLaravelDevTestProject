<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Our Products</h1>
            <p class="text-lg text-gray-600">
                Discover amazing products from our collection
                <span class="font-semibold text-blue-600">{{ $products->total() }}</span> items available
            </p>
        </div>

        <!-- Filters and Search Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Search Products
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by title, description, or category..."
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150"
                        >
                        @if($search)
                            <button
                                wire:click="$set('search', '')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category
                    </label>
                    <select
                        wire:model.live="selectedCategory"
                        id="category"
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150"
                    >
                        <option value="">All Categories</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort Dropdown -->
                <div x-data="{ open: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sort By
                    </label>
                    <div class="relative">
                        <button
                            @click="open = !open"
                            @click.away="open = false"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between transition duration-150"
                        >
                            <span class="capitalize text-sm">
                                {{ $sortBy === 'created_at' ? 'Date Added' : ($sortBy === 'title' ? 'Title' : 'Price') }}
                                <span class="text-gray-500 ml-1">
                                    ({{ $sortDirection === 'asc' ? '↑' : '↓' }})
                                </span>
                            </span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                            style="display: none;"
                        >
                            <button
                                wire:click="sortBy('created_at')"
                                @click="open = false"
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 first:rounded-t-lg text-sm transition duration-150 {{ $sortBy === 'created_at' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700' }}"
                            >
                                Date Added
                            </button>
                            <button
                                wire:click="sortBy('title')"
                                @click="open = false"
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 text-sm transition duration-150 {{ $sortBy === 'title' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700' }}"
                            >
                                Title
                            </button>
                            <button
                                wire:click="sortBy('price')"
                                @click="open = false"
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 last:rounded-b-lg text-sm transition duration-150 {{ $sortBy === 'price' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700' }}"
                            >
                                Price
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filters and Clear Button -->
            @if($search || $selectedCategory)
                <div class="mt-4 flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">Active Filters:</span>
                    <div class="flex gap-2 flex-wrap">
                        @if($search)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                Search: "{{ $search }}"
                                <button wire:click="$set('search', '')" class="ml-2 hover:text-blue-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        @if($selectedCategory)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                {{ $selectedCategory }}
                                <button wire:click="$set('selectedCategory', '')" class="ml-2 hover:text-blue-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                    </div>
                    <button
                        wire:click="clearFilters"
                        class="ml-auto text-sm text-red-600 hover:text-red-800 font-medium transition duration-150"
                    >
                        Clear All
                    </button>
                </div>
            @endif
        </div>

        <!-- Loading Indicator -->
        <div wire:loading class="mb-6">
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="font-medium">Loading products...</span>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($products as $product)
                <div
                    class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1"
                    x-data="{ showFullDescription: false }"
                >
                    <!-- Product Image -->
                    <div class="relative aspect-square bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden group">
                        @php
                            $imageUrl = $product->images->first()?->url;
                            $isValidImage = $imageUrl && !str_starts_with($imageUrl, 'data:image/gif;base64');
                        @endphp

                        @if($isValidImage)
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $product->title }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                loading="lazy"
                                onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gray-100\'><svg class=\'w-20 h-20 text-gray-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';"
                            >
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                <svg class="w-20 h-20 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm text-gray-400">No Image</span>
                            </div>
                        @endif

                        <!-- Price Badge -->
                        <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-lg">
                            <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-5">
                        <!-- Category Badge -->
                        @if($product->category)
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full mb-3">
                                {{ Str::limit($product->category, 30) }}
                            </span>
                        @endif

                        <!-- Title -->
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 min-h-[3.5rem]" title="{{ $product->title }}">
                            {{ $product->title }}
                        </h3>

                        <!-- Description -->
                        @if($product->description)
                            <div class="text-sm text-gray-600 mb-4">
                                <p
                                    x-show="!showFullDescription"
                                    class="line-clamp-3"
                                >
                                    {{ $product->description }}
                                </p>
                                <p
                                    x-show="showFullDescription"
                                    x-cloak
                                    class="whitespace-pre-line"
                                >
                                    {{ $product->description }}
                                </p>

                                @if(strlen($product->description) > 150)
                                    <button
                                        @click="showFullDescription = !showFullDescription"
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium mt-1 transition duration-150"
                                    >
                                        <span x-show="!showFullDescription">Read more →</span>
                                        <span x-show="showFullDescription" x-cloak>Read less ←</span>
                                    </button>
                                @endif
                            </div>
                        @endif

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $product->created_at->diffForHumans() }}</span>
                            </div>

                            @if($product->images->count() > 1)
                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $product->images->count() }} images</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-md p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No products found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your search or filters to find what you're looking for.</p>
                        @if($search || $selectedCategory)
                            <button
                                wire:click="clearFilters"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150"
                            >
                                Clear Filters
                            </button>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>

        <!-- Results Summary -->
        <div class="mt-4 text-center text-sm text-gray-600">
            Showing
            <span class="font-semibold">{{ $products->firstItem() ?? 0 }}</span>
            to
            <span class="font-semibold">{{ $products->lastItem() ?? 0 }}</span>
            of
            <span class="font-semibold">{{ $products->total() }}</span>
            results
        </div>
    </div>
</div>
