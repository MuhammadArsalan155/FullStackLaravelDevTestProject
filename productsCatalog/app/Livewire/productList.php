<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Title('Products - Browse Our Collection')]
class ProductList extends Component
{
    use WithPagination;

    #[Url(as: 'sort')]
    public string $sortBy = 'created_at';

    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public string $selectedCategory = '';

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when category changes
     */
    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    /**
     * Sort by a specific field
     */
    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Clear all filters
     */
    public function clearFilters(): void
    {
        $this->reset(['search', 'selectedCategory', 'sortBy', 'sortDirection']);
        $this->resetPage();
    }

    /**
     * Get available categories
     */
    #[Computed]
    public function categories()
    {
        return Product::query()
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category')
            ->sort()
            ->values();
    }

    /**
     * Render the component
     */
    public function render()
    {
        $products = Product::with('images')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category', $this->selectedCategory);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(25);

        return view('livewire.product-list', [
            'products' => $products,
        ]);
    }
}
