// Quote management functionality
class QuoteManager {
    constructor() {
        this.currentQuotation = null;
        this.initEventListeners();
    }

    initEventListeners() {
        // Quote add button
        const quotesAddBtn = document.getElementById('quotes-add-btn');
        if (quotesAddBtn) {
            quotesAddBtn.addEventListener('click', () => this.createQuotationModal());
        }

        // Quote search with debounce
        const quotesSearch = document.getElementById('quotes-search');
        if (quotesSearch) {
            let searchDebounce;
            quotesSearch.addEventListener('input', (e) => {
                clearTimeout(searchDebounce);
                const query = e.target.value.trim();
                searchDebounce = setTimeout(() => this.loadQuotes(query), 350);
            });
        }
    }

    async createQuotationModal() {
        try {
            const response = await fetch('/crm/quotes/create-modal');
            if (!response.ok) throw new Error('Failed to load create modal');
            
            const modalContent = await response.text();
            this.showModal('Create New Quotation', modalContent);
            await this.loadRawMaterials();
            await this.loadPackaging();
            
            // Attach form submit handler
            const basicForm = document.getElementById('quotation-basic-form');
            if (basicForm) {
                basicForm.addEventListener('submit', (e) => this.startQuotation(e));
            }
        } catch (error) {
            this.showToaster('error', 'Error', 'Failed to load quotation form: ' + error.message);
        }
    }

    async editQuoteModal(quoteId) {
        try {
            const response = await fetch(`/crm/quotes/${quoteId}/edit-modal`);
            if (!response.ok) throw new Error('Failed to load edit modal');
            
            const modalContent = await response.text();
            this.showModal('Edit Quotation', modalContent);
            
            // Attach form submit handler
            const editForm = document.getElementById('quote-edit-form');
            if (editForm) {
                editForm.addEventListener('submit', (e) => this.updateQuote(e, quoteId));
            }
        } catch (error) {
            this.showToaster('error', 'Error', 'Failed to load edit form: ' + error.message);
        }
    }

    async startQuotation(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const token = this.getCSRFToken();
            const response = await fetch('/api/quotes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to create quotation');
            }
            
            this.currentQuotation = await response.json();
            this.showStep(2);
        } catch (error) {
            this.showToaster('error', 'Error', 'Failed to create quotation: ' + error.message);
        }
    }

    async updateQuote(e, quoteId) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const token = this.getCSRFToken();
            const response = await fetch(`/api/quotes/${quoteId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to update quotation');
            }
            
            this.hideModal();
            this.loadQuotes();
            this.showToaster('success', 'Success', 'Quotation updated successfully!');
        } catch (error) {
            this.showToaster('error', 'Error', 'Failed to update quotation: ' + error.message);
        }
    }

    async loadRawMaterials() {
        const select = document.getElementById('raw-material-select');
        if (!select) return;
        
        try {
            const response = await fetch('/api/raw-materials');
            if (response.ok) {
                const materials = await response.json();
                select.innerHTML = '<option value="">Select Raw Material</option>' + 
                    materials.map(m => 
                        `<option value="${m.id}" data-cost="${m.unit_cost}" data-unit="${m.unit_of_measure}">
                            ${this.escapeHtml(m.name)} (€${m.unit_cost}/${m.unit_of_measure})
                         </option>`
                    ).join('');
            }
        } catch (error) {
            console.error('Failed to load raw materials:', error);
        }
    }

    async loadPackaging() {
        const select = document.getElementById('packaging-select');
        if (!select) return;
        
        try {
            const response = await fetch('/api/packaging');
            if (response.ok) {
                const packaging = await response.json();
                select.innerHTML = '<option value="">Select Packaging</option>' + 
                    packaging.map(p => 
                        `<option value="${p.id}" data-volume="${p.volume}" data-cost="${p.unit_cost}">
                            ${this.escapeHtml(p.name)} (${p.volume}${p.volume_unit}) - €${p.unit_cost}
                         </option>`
                    ).join('');
            }
        } catch (error) {
            console.error('Failed to load packaging:', error);
        }
    }

    async addRawMaterialToQuotation() {
        console.log(this.currentQuotation)
        if (!this.currentQuotation) {
            this.showToaster('error', 'Error', 'No active quotation found');
            return;
        }
        
        const select = document.getElementById('raw-material-select');
        const percentageInput = document.getElementById('material-percentage');
        const materialId = select.value;
        const percentage = parseFloat(percentageInput.value);
        
        if (!materialId || !percentage) {
            this.showToaster('error', 'Error', 'Please select a material and enter percentage');
            return;
        }
        
        try {
            const token = this.getCSRFToken();
            const response = await fetch(`/api/quotes/${this.currentQuotation.id}/add-raw-material`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    raw_material_id: materialId,
                    percentage: percentage
                })
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Failed to add material');
            }
            
            await this.updateMaterialsList();
            percentageInput.value = '';
            this.showToaster('success', 'Success', 'Material added successfully');
            
        } catch (error) {
            this.showToaster('error', 'Error', error.message);
        }
    }

    async addPackagingToQuotation() {
        if (!this.currentQuotation) {
            this.showToaster('error', 'Error', 'No active quotation found');
            return;
        }
        
        const select = document.getElementById('packaging-select');
        const quantityInput = document.getElementById('packaging-quantity');
        const packagingId = select.value;
        const quantity = parseInt(quantityInput.value);
        
        if (!packagingId || !quantity) {
            this.showToaster('error', 'Error', 'Please select packaging and enter quantity');
            return;
        }
        
        try {
            const token = this.getCSRFToken();
            const response = await fetch(`/api/quotes/${this.currentQuotation.id}/add-packaging`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    packaging_id: packagingId,
                    quantity: quantity
                })
            });
            
            if (!response.ok) throw new Error('Failed to add packaging');
            
            await this.updatePackagingList();
            quantityInput.value = 1;
            this.showToaster('success', 'Success', 'Packaging added successfully');
            
        } catch (error) {
            this.showToaster('error', 'Error', error.message);
        }
    }

    async updateMaterialsList() {
        if (!this.currentQuotation) return;
        
        try {
            const response = await fetch(`/api/quotes/${this.currentQuotation.id}`);
            if (response.ok) {
                const quotation = await response.json();
                const tbody = document.getElementById('selected-materials');
                const totalEl = document.getElementById('percentage-total');
                
                if (tbody) {
                    tbody.innerHTML = quotation.items
                        .filter(item => item.item_type === 'raw_material')
                        .map(item => `
                            <tr>
                                <td>${this.escapeHtml(item.item_name)}</td>
                                <td>${item.percentage}%</td>
                                <td>€${(item.total_cost).toFixed(2)}</td>
                                <td><button class="btn btn-sm btn-danger" onclick="quoteManager.removeQuotationItem(${item.id})">Remove</button></td>
                            </tr>
                        `).join('');
                }
                
                if (totalEl) {
                    const totalPercentage = quotation.items
                        .filter(item => item.item_type === 'raw_material')
                        .reduce((sum, item) => sum + parseFloat(item.percentage), 0);
                    
                    totalEl.textContent = `Total Percentage: ${totalPercentage}%`;
                    totalEl.className = totalPercentage === 100 ? 'text-success' : totalPercentage > 100 ? 'text-danger' : '';
                }
            }
        } catch (error) {
            console.error('Failed to update materials list:', error);
        }
    }

    async updatePackagingList() {
        if (!this.currentQuotation) return;
        
        try {
            const response = await fetch(`/api/quotes/${this.currentQuotation.id}`);
            if (response.ok) {
                const quotation = await response.json();
                const tbody = document.getElementById('selected-packaging');
                
                if (tbody) {
                    tbody.innerHTML = quotation.items
                        .filter(item => item.item_type === 'packaging')
                        .map(item => `
                            <tr>
                                <td>${this.escapeHtml(item.item_name)}</td>
                                <td>${item.quantity}</td>
                                <td>${item.quantity} units</td>
                                <td>€${(item.total_cost || 0).toFixed(2)}</td>
                                <td><button class="btn btn-sm btn-danger" onclick="quoteManager.removeQuotationItem(${item.id})">Remove</button></td>
                            </tr>
                        `).join('');
                }
            }
        } catch (error) {
            console.error('Failed to update packaging list:', error);
        }
    }

    async calculateQuotation() {
        if (!this.currentQuotation) return;
        
        try {
            const token = this.getCSRFToken();
            const response = await fetch(`/api/quotes/${this.currentQuotation.id}/calculate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    manufacturing_cost_percent: 30,
                    risk_cost_percent: 5,
                    profit_margin_percent: 30,
                    tax_rate: 19
                })
            });
            
            if (!response.ok) throw new Error('Failed to calculate quotation');
            
            const result = await response.json();
            this.updateCalculationResult(result);
            this.showStep(4);
            
        } catch (error) {
            this.showToaster('error', 'Error', error.message);
        }
    }

    updateCalculationResult(result) {
        const elements = {
            'raw-material-cost': result.total_raw_material_cost,
            'packaging-cost': result.total_packaging_cost,
            'manufacturing-cost': result.manufacturing_cost,
            'risk-cost': result.risk_cost,
            'subtotal': result.subtotal,
            'profit-amount': result.subtotal * (result.profit_margin / 100),
            'total-without-tax': result.subtotal * (1 + result.profit_margin / 100),
            'tax-amount': result.tax_amount,
            'total-amount': result.total_amount,
            'price-per-unit': result.total_amount / result.quantity
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = `€${value}`;
            }
        });
    }

    async saveQuotation() {
        if (!this.currentQuotation) return;
        
        try {
            const token = this.getCSRFToken();
            const response = await fetch(`/api/quotes/${this.currentQuotation.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    status: 'sent',
                    notes: 'Quotation calculated and ready for customer'
                })
            });
            
            if (!response.ok) throw new Error('Failed to save quotation');
            
            this.showToaster('success', 'Success', 'Quotation saved successfully!');
            this.hideModal();
            this.loadQuotes();
            
        } catch (error) {
            this.showToaster('error', 'Error', error.message);
        }
    }

    async removeQuotationItem(itemId) {
        if (!this.currentQuotation) return;
        
        try {
            const token = this.getCSRFToken();
            const response = await fetch(`/api/quotation-items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
            
            if (!response.ok) throw new Error('Failed to remove item');
            
            await this.updateMaterialsList();
            await this.updatePackagingList();
            this.showToaster('success', 'Success', 'Item removed successfully');
            
        } catch (error) {
            this.showToaster('error', 'Error', error.message);
        }
    }

    async deleteQuote(quoteId) {
        if (!(await this.showConfirm('Delete this quote?'))) return;
        
        try {
            const token = this.getCSRFToken();
            const response = await fetch(`/api/quotes/${quoteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
            
            if (!response.ok) throw new Error('Delete failed');
            
            this.loadQuotes();
            this.showToaster('success', 'Success', 'Quote deleted successfully');
            
        } catch (error) {
            this.showToaster('error', 'Error', 'Failed to delete quote: ' + error.message);
        }
    }

    async loadQuotes(query = '') {
        const tbody = document.getElementById('crm-quotes-tbody');
        if (!tbody) return;
        
        tbody.innerHTML = '<tr><td colspan="6">Loading...</td></tr>';
        
        try {
            const url = '/api/quotes' + (query ? ('?q=' + encodeURIComponent(query)) : '');
            const response = await fetch(url);
            
            if (!response.ok) throw new Error('Failed to fetch quotes');
            
            const data = await response.json();
            const items = data.data || data;
            
            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="6">No quotes found</td></tr>';
                return;
            }
            
            tbody.innerHTML = items.map(quote => `
                <tr>
                    <td>
                    <a href="/quotes/${quote.id}" class="text-decoration-none text-primary fw-semibold">
                        ${quote.quote_number || quote.id}
                    </a>
                </td>
                    <td>${this.escapeHtml(quote.customer?.company_name || '')}</td>
                    <td>${this.escapeHtml(quote.product_name || '')}</td>
                    <td>€${Number(quote.total_amount || 0).toLocaleString()}</td>
                    <td><span class="badge ${this.getStatusBadgeClass(quote.status)}">${quote.status}</span></td>
                    <td>
                        <a href="/quotes/${quote.id}/edit" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger" onclick="quoteManager.deleteQuote(${quote.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
            
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="6">Error loading quotes: ${this.escapeHtml(error.message)}</td></tr>`;
        }
    }

    // Utility methods
    getStatusBadgeClass(status) {
        const statusClasses = {
            'draft': 'badge-secondary',
            'sent': 'badge-warning',
            'accepted': 'badge-success',
            'rejected': 'badge-danger',
            'expired': 'badge-dark'
        };
        return statusClasses[status] || 'badge-secondary';
    }

    showStep(stepNumber) {
        document.querySelectorAll('.flow-step').forEach(step => {
            step.style.display = 'none';
        });
        
        const step = document.getElementById(`step-${stepNumber}`);
        if (step) {
            step.style.display = 'block';
        }
        
        if (stepNumber === 2) this.updateMaterialsList();
        if (stepNumber === 3) this.updatePackagingList();
    }

    showModal(title, content) {
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        
        if (modalTitle) modalTitle.textContent = title;
        if (modalBody) modalBody.innerHTML = content;
        
        this.hideModal(); // Ensure modal is hidden first
        setTimeout(() => {
            const modalOverlay = document.getElementById('modal-overlay');
            if (modalOverlay) modalOverlay.style.display = 'flex';
        }, 50);
    }

    hideModal() {
        const modalOverlay = document.getElementById('modal-overlay');
        if (modalOverlay) modalOverlay.style.display = 'none';
    }

    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async showConfirm(message) {
        return confirm(message);
    }

    showToaster(type, title, message) {
        // Implement your toaster notification system here
        console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
        alert(`${title}: ${message}`); // Fallback
    }
}

// Initialize quote manager
const quoteManager = new QuoteManager();

// Make available globally
window.quoteManager = quoteManager;
window.createQuotationModal = () => quoteManager.createQuotationModal();
window.editQuote = (id) => quoteManager.editQuoteModal(id);
window.deleteQuote = (id) => quoteManager.deleteQuote(id);
window.showStep = (step) => quoteManager.showStep(step);
window.addRawMaterialToQuotation = () => quoteManager.addRawMaterialToQuotation();
window.addPackagingToQuotation = () => quoteManager.addPackagingToQuotation();
window.calculateQuotation = () => quoteManager.calculateQuotation();
window.saveQuotation = () => quoteManager.saveQuotation();