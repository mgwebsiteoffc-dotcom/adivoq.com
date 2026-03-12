@extends('layouts.tenant')

@section('title', 'Service Catalog - Settings')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="serviceManager()" @init="initializeServices()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Service Catalog</h1>
            <p class="text-gray-600 mt-2">Create reusable services with predefined HSN codes and pricing for quick invoice creation.</p>
        </div>
        <button 
            @click="openCreateForm()"
            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700 transition"
        >
            + Add Service
        </button>
    </div>

    <!-- Services List -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div x-show="services.length === 0" class="p-8 text-center">
            <p class="text-gray-500 mb-4">No services created yet. Add your first service to get started.</p>
            <button 
                @click="openCreateForm()"
                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700 transition"
            >
                Create First Service
            </button>
        </div>

        <div x-show="services.length > 0" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Service Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">HSN Code</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Price</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Unit</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Tax Rate</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="service in services" :key="service.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900" x-text="service.name"></p>
                                <p class="text-xs text-gray-500" x-text="service.description || 'No description'"></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-lg font-mono text-xs font-semibold" x-text="service.hsn_code"></span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">₹<span x-text="parseFloat(service.default_unit_price).toFixed(2)"></span></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-700" x-text="service.unit"></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-700"><span x-text="service.tax_rate"></span>%</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button 
                                        @click="openEditForm(service)"
                                        class="text-indigo-600 hover:text-indigo-700 font-medium text-sm transition"
                                    >
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <button 
                                        @click="deleteService(service.id)"
                                        class="text-red-600 hover:text-red-700 font-medium text-sm transition"
                                    >
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Create/Edit Form Modal -->
        <div x-show="showCreateForm" @click.self="showCreateForm = false" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-transition>
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-xl">
                    <h2 class="text-base font-black text-gray-900" x-text="editingServiceId ? 'Edit Service' : 'Add New Service'"></h2>
                    <button @click="showCreateForm = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form @submit="submitService" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Service Name *</label>
                        <input 
                            type="text" 
                            x-model="formData.name"
                            placeholder="e.g., Web Design Service, Consulting"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                        <textarea 
                            x-model="formData.description"
                            placeholder="Brief description of your service"
                            rows="2"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">HSN/SAC Code *</label>
                        <div class="relative">
                            <input 
                                type="text"
                                x-model="hsnSearchQuery"
                                @input="searchHsn()"
                                placeholder="Search HSN code..."
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                            <div x-show="hsnSearchResults.length > 0" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto z-50">
                                <template x-for="hsn in hsnSearchResults" :key="hsn.id">
                                    <div 
                                        @click="selectHsn(hsn)"
                                        class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition"
                                    >
                                        <p class="font-mono font-bold text-indigo-600 text-sm" x-text="hsn.code"></p>
                                        <p class="text-xs text-gray-600" x-text="hsn.description"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" x-model="formData.hsn_sac_code_id">
                        <p x-show="formData.hsn_sac_code_id" class="text-xs text-green-600 mt-2"><i class="fas fa-check mr-1"></i>HSN code selected</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Default Price *</label>
                            <input 
                                type="number"
                                step="0.01"
                                min="0"
                                x-model="formData.default_unit_price"
                                placeholder="0.00"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Unit *</label>
                            <select x-model="formData.unit" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                                <option value="">Select unit</option>
                                <option value="per item">Per Item</option>
                                <option value="per hour">Per Hour</option>
                                <option value="per day">Per Day</option>
                                <option value="per project">Per Project</option>
                                <option value="per month">Per Month</option>
                                <option value="per quantity">Per Quantity</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tax Rate (%) *</label>
                        <select x-model="formData.tax_rate" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                            <option value="">Select tax rate</option>
                            <option value="0">0% (Exempt)</option>
                            <option value="5">5%</option>
                            <option value="12">12%</option>
                            <option value="18">18%</option>
                            <option value="28">28%</option>
                        </select>
                    </div>

                    <div class="flex gap-3 justify-end pt-4 border-t border-gray-200">
                        <button 
                            type="button"
                            @click="showCreateForm = false"
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-black hover:bg-gray-50 transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700 transition"
                        >
                            Save Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Initial services data from server
const initialServices = {!! json_encode($services->map(fn ($s) => [
    'id' => $s->id,
    'name' => $s->name,
    'description' => $s->description,
    'hsn_code' => $s->hsnCode->code,
    'hsn_description' => $s->hsnCode->description,
    'default_unit_price' => $s->default_unit_price,
    'unit' => $s->unit,
    'tax_rate' => $s->tax_rate,
    'is_active' => $s->is_active,
    'created_at' => $s->created_at->format('Y-m-d H:i'),
])) !!};

document.addEventListener('alpine:init', () => {
    Alpine.data('serviceManager', () => ({
        services: initialServices,
        showCreateForm: false,
        editingServiceId: null,
        formData: {
            name: '',
            description: '',
            hsn_sac_code_id: '',
            default_unit_price: '',
            unit: '',
            tax_rate: '',
        },
        hsnSearchQuery: '',
        hsnSearchResults: [],

        async initializeServices() {
            // If services are empty, try loading via API
            if (this.services.length === 0) {
                await this.loadServices();
            }
        },

        openCreateForm() {
            this.editingServiceId = null;
            this.resetForm();
            this.showCreateForm = true;
        },

        openEditForm(service) {
            this.editingServiceId = service.id;
            this.formData = {
                name: service.name,
                description: service.description,
                hsn_sac_code_id: service.hsn_sac_code_id || '',
                default_unit_price: service.default_unit_price,
                unit: service.unit,
                tax_rate: service.tax_rate,
            };
            this.hsnSearchQuery = `${service.hsn_code}`;
            this.showCreateForm = true;
        },

        async loadServices() {
            const response = await fetch('{{ route("dashboard.settings.services.api-list") }}');
            const data = await response.json();
            this.services = data.data || [];
        },

        async searchHsn() {
            if (this.hsnSearchQuery.length < 2) {
                this.hsnSearchResults = [];
                return;
            }
            const response = await fetch(`{{ route("dashboard.settings.services.search-hsn") }}?search=${this.hsnSearchQuery}`);
            const data = await response.json();
            this.hsnSearchResults = data.data || [];
        },

        selectHsn(hsn) {
            this.formData.hsn_sac_code_id = hsn.id;
            this.hsnSearchQuery = `${hsn.code} - ${hsn.description}`;
            this.hsnSearchResults = [];
        },

        async submitService(e) {
            e.preventDefault();
            try {
                const url = this.editingServiceId 
                    ? `{{ route("dashboard.settings.services.update", ":id") }}`.replace(':id', this.editingServiceId)
                    : '{{ route("dashboard.settings.services.api-store") }}';
                
                const method = this.editingServiceId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.formData),
                });
                const data = await response.json();
                if (response.ok) {
                    const message = this.editingServiceId ? 'Service updated successfully!' : 'Service created successfully!';
                    this.showNotification(message, 'success');
                    this.showCreateForm = false;
                    this.resetForm();
                    await new Promise(resolve => setTimeout(resolve, 500));
                    this.loadServices();
                } else {
                    this.showNotification(data.message || 'Error saving service', 'error');
                }
            } catch (error) {
                console.error(error);
                this.showNotification('An error occurred while saving the service', 'error');
            }
        },

        async deleteService(id) {
            if (!confirm('Are you sure you want to delete this service?')) return;
            try {
                const response = await fetch(`{{ route("dashboard.settings.services.api-destroy", ":id") }}`.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                if (response.ok) {
                    const data = await response.json();
                    this.showNotification(data.message || 'Service deleted successfully!', 'success');
                    this.loadServices();
                } else {
                    this.showNotification('Error deleting service', 'error');
                }
            } catch (error) {
                console.error(error);
                this.showNotification('An error occurred while deleting the service', 'error');
            }
        },

        resetForm() {
            this.editingServiceId = null;
            this.formData = {
                name: '',
                description: '',
                hsn_sac_code_id: '',
                default_unit_price: '',
                unit: '',
                tax_rate: '',
            };
            this.hsnSearchQuery = '';
            this.hsnSearchResults = [];
        },

        showNotification(message, type = 'success') {
            const notificationId = 'notification-' + Date.now();
            const bgColor = type === 'success' ? 'bg-green-50' : 'bg-red-50';
            const borderColor = type === 'success' ? 'border-green-200' : 'border-red-200';
            const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
            const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            const closeColor = type === 'success' ? 'text-green-400 hover:text-green-600' : 'text-red-400 hover:text-red-600';

            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = `mb-4 ${bgColor} border ${borderColor} ${textColor} rounded-xl p-4 flex items-start`;
            notification.innerHTML = `
                <i class="fas ${icon} mt-0.5 mr-3 ${iconColor}"></i>
                <div class="flex-1 text-sm font-semibold">${message}</div>
                <button onclick="document.getElementById('${notificationId}').remove()" class="${closeColor}">
                    <i class="fas fa-times"></i>
                </button>
            `;

            const mainContent = document.querySelector('main') || document.body;
            mainContent.insertBefore(notification, mainContent.firstChild);

            setTimeout(() => {
                const elem = document.getElementById(notificationId);
                if (elem) elem.remove();
            }, 5000);
        }
    }))
});
</script>
@endsection
