<div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Menu Management</h2>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    <div class="mb-6">
        <label class="block text-sm font-medium mb-2">Menu Location</label>
        <select wire:model.live="selectedLocation" class="rounded-md border-gray-300">
            <option value="header">Header</option>
            <option value="mobile">Mobile</option>
            <option value="footer">Footer</option>
        </select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">{{ $editingId ? 'Edit' : 'Add' }} Menu Item</h3>
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Label *</label>
                    <input type="text" wire:model="label" class="w-full rounded-md border-gray-300">
                    @error('label') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Route Name</label>
                    <input type="text" wire:model="route" class="w-full rounded-md border-gray-300" placeholder="e.g. home">
                    @error('route') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">OR Custom URL</label>
                    <input type="text" wire:model="url" class="w-full rounded-md border-gray-300">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Order *</label>
                        <input type="number" wire:model="order" class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Target</label>
                        <select wire:model="target" class="w-full rounded-md border-gray-300">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Visibility</label>
                    <select wire:model="show_when_logged_in" class="w-full rounded-md border-gray-300">
                        <option value="">Always Show</option>
                        <option value="1">Logged In Only</option>
                        <option value="0">Logged Out Only</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_active" id="is_active" class="rounded border-gray-300">
                    <label for="is_active" class="ml-2 text-sm">Active</label>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </button>
                    @if($editingId)
                        <button type="button" wire:click="resetForm" class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500">Cancel</button>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Menu Items ({{ ucfirst($selectedLocation) }})</h3>
            <div class="space-y-2">
                @foreach($menuItems as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <span class="font-medium">{{ $item->label }}</span>
                            <span class="text-sm text-gray-500 ml-2">(Order: {{ $item->order }})</span>
                            @if(!$item->is_active)
                                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded ml-2">Inactive</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="edit({{ $item->id }})" class="text-indigo-600 hover:text-indigo-800">Edit</button>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Delete this menu item?" class="text-red-600 hover:text-red-800">Delete</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
