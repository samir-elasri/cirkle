// Define pattern constants
const ITEM_PATTERN = '[a-zA-Z0-9 \\-]+';
const ITEM_PATTERN_REGEX = new RegExp(`^${ITEM_PATTERN}$`);

// Translations object outside the class
const TRANSLATIONS = {
  en: {
    title: 'Item List',
    placeholder: 'Add an item...',
    addButton: 'Add',
    submitButton: 'Submit List',
    emptyList: 'No items added yet',
    itemMaxLength: 'Items must be 7 characters or less',
    itemDuplicate: 'This item already exists in the list',
    invalidFormat: 'Only letters, numbers, spaces and dashes allowed'
  },
  fr: {
    title: 'Liste d\'éléments',
    placeholder: 'Ajouter un élément...',
    addButton: 'Ajouter',
    submitButton: 'Soumettre la liste',
    emptyList: 'Aucun élément ajouté',
    itemMaxLength: 'Les éléments doivent comporter 7 caractères ou moins',
    itemDuplicate: 'Cet élément existe déjà dans la liste',
    invalidFormat: 'Seuls les lettres, chiffres, espaces et tirets sont autorisés'
  }
};
  
class ListInputElement extends HTMLElement {
  constructor() {
    super();
    
    // Use document language or default to French
    const docLang = document.documentElement.lang;
    this.lang = (docLang && TRANSLATIONS[docLang]) ? docLang : 'fr';
    
    // Store list items
    this.items = [];
    
    // Get field name from attribute
    this.fieldName = this.getAttribute('name') || 'items';
    
    // Bind methods to preserve context
    this.addItem = this.addItem.bind(this);
    this.handleKeyPress = this.handleKeyPress.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }
  
  connectedCallback() {
    // Render component HTML directly without shadow DOM
    this.innerHTML = this.createTemplate();
    
    // Set up event listeners when connected to DOM
    this.setupEventListeners();
    
    // Set form association
    if (this.closest('form')) {
      this.form = this.closest('form');
      this.formId = this.form.id;
      
      // If the form doesn't have an ID, create one
      if (!this.formId) {
        this.formId = 'form_' + Math.random().toString(36).substr(2, 9);
        this.form.id = this.formId;
      }
    }
  }
  
  
  createTemplate() {
    const text = TRANSLATIONS[this.lang] || TRANSLATIONS.fr;
    
    return `
      <div class="list-input-container" role="form" aria-labelledby="listTitle">
        <h2 class="list-title" id="listTitle">${text.title}</h2>
        
        <div class="input-container">
          <input type="text" 
               id="newItem" 
               placeholder="${text.placeholder}" 
               aria-label="${text.placeholder}"
               maxlength="7"
               aria-describedby="errorMessage"
               autocomplete="off"
               pattern="${ITEM_PATTERN}">
          <button type="button" 
                class="call-to-action"
                id="addButton" 
                aria-label="${text.addButton}">
            ${text.addButton}
          </button>
        </div>
        
        <div id="errorMessage" class="error-message" aria-live="assertive"></div>
        
        <div class="item-list" 
           id="itemList" 
           role="list" 
           aria-label="${text.title}">
          <div class="empty-list">${text.emptyList}</div>
        </div>
        
        <!-- Hidden container for individual item inputs -->
        <div id="hiddenInputsContainer"></div>

      </div>
    `;
  }
  
  setupEventListeners() {
    // Add button click handler
    this.querySelector('#addButton').addEventListener('click', this.addItem);
    
    // Enter key handler for the input
    this.querySelector('#newItem').addEventListener('keypress', this.handleKeyPress);
    
    // Input validation
    this.querySelector('#newItem').addEventListener('input', (e) => {
      const input = e.target;
      if (input.validity.patternMismatch) {
        this.showError(TRANSLATIONS[this.lang].invalidFormat);
        input.classList.add('invalid');
      } else {
        this.clearError();
        input.classList.remove('invalid');
      }
    });
  }
  
  handleKeyPress(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      this.addItem();
    }
  }
  
  handleSubmit() {
    // Check if the list is empty
    if (this.items.length === 0) {
      this.reportValidity();
    }
    
    // Dispatch a custom event when submit is clicked
    const event = new CustomEvent('list-submit', {
      bubbles: true,
      composed: true,
      detail: {
        items: this.items,
        value: this.items.join(',')
      }
    });
    this.dispatchEvent(event);
  }
  
  reportValidity() {
    // This method can be called by forms for validation
    const valid = this.items.length > 0;
    
    // If empty, show an error
    if (!valid) {
      const text = TRANSLATIONS[this.lang];
      this.showError(text.emptyList || 'Please add at least one item');
      return false;
    }
    
    this.clearError();
    return true;
  }
  
  validateItem(itemValue) {
    const text = TRANSLATIONS[this.lang];
    
    // Check if item is empty
    if (!itemValue) {
      return false;
    }
    
    // Check length (hardcoded to 7 characters)
    if (itemValue.length > 7) {
      this.showError(text.itemMaxLength);
      return false;
    }
    
    // Check for duplicates
    if (this.items.includes(itemValue)) {
      this.showError(text.itemDuplicate);
      return false;
    }
    
    // Validate only allows letters, numbers, spaces and dashes
    if (!ITEM_PATTERN_REGEX.test(itemValue)) {
      this.showError(text.invalidFormat);
      return false;
    }
    
    // Clear any existing error
    this.clearError();
    return true;
  }
  
  showError(message) {
    const errorMessage = this.querySelector('#errorMessage');
    errorMessage.textContent = message;
  }
  
  clearError() {
    const errorMessage = this.querySelector('#errorMessage');
    errorMessage.textContent = '';
  }
  
  addItem() {
    const input = this.querySelector('#newItem');
    const itemValue = input.value.trim();
    
    if (this.validateItem(itemValue)) {
      this.items.push(itemValue);
      input.value = '';
      this.renderItems();
      this.updateHiddenInput();
      input.focus();
    }
  }
  
  removeItem(index) {
    this.items.splice(index, 1);
    this.renderItems();
    this.updateHiddenInput();
    this.querySelector('#newItem').focus();
  }
  
  renderItems() {
    const itemList = this.querySelector('#itemList');
    
    // Clear the list first
    itemList.innerHTML = '';
    
    // Show empty message if no items
    if (this.items.length === 0) {
      const text = TRANSLATIONS[this.lang];
      const emptyMessage = document.createElement('div');
      emptyMessage.className = 'empty-list';
      emptyMessage.textContent = text.emptyList;
      itemList.appendChild(emptyMessage);
      return;
    }
    
    // Render all items
    this.items.forEach((item, index) => {
      const itemElement = document.createElement('div');
      itemElement.className = 'list-item';
      itemElement.setAttribute('role', 'listitem');
      
      const itemText = document.createElement('span');
      itemText.textContent = item;
      
      const removeButton = document.createElement('button');
      removeButton.className = 'call-to-action';
      removeButton.textContent = '×';
      removeButton.setAttribute('aria-label', `Remove ${item}`);
      
      // Using arrow function to avoid binding issues
      removeButton.addEventListener('click', () => this.removeItem(index));
      
      removeButton.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.removeItem(index);
        }
      });
      
      removeButton.setAttribute('tabindex', '0');
      
      itemElement.appendChild(itemText);
      itemElement.appendChild(removeButton);
      itemList.appendChild(itemElement);
    });
  }
  
  updateHiddenInput() {
    const container = this.querySelector('#hiddenInputsContainer');
    
    // Clear previous hidden inputs
    container.innerHTML = '';
    
    // Create an individual hidden input for each item
    this.items.forEach((item, index) => {
      const hiddenInput = document.createElement('input');
      hiddenInput.type = 'hidden';
      hiddenInput.name = `${this.fieldName}[]`;
      hiddenInput.value = item;
      hiddenInput.id = `${this.fieldName}_${index}`;
      
      // Associate with form if needed
      if (this.formId) {
        hiddenInput.setAttribute('form', this.formId);
      }
      
      container.appendChild(hiddenInput);
    });
    
    // Add a required indicator if there are no items
    if (this.items.length === 0) {
      const requiredIndicator = document.createElement('input');
      requiredIndicator.type = 'hidden';
      requiredIndicator.name = `${this.fieldName}_required`;
      requiredIndicator.required = true;
      container.appendChild(requiredIndicator);
    }
    
    // Dispatch an event when the value changes
    const event = new CustomEvent('list-change', {
      bubbles: true,
      composed: true,
      detail: {
        items: this.items,
        value: this.items
      }
    });
    this.dispatchEvent(event);
  }
  
  // Public API - minimal set of needed methods
  getItems() {
    return [...this.items];
  }
  
  clear() {
    this.items = [];
    this.renderItems();
    this.updateHiddenInput();
    this.clearError();
  }
  
  setItems(newItems) {
    if (Array.isArray(newItems)) {
      // Filter items to ensure they meet validation criteria
      this.items = newItems
        .filter(item => typeof item === 'string')
        .filter(item => item.length <= 7)
        .filter(item => ITEM_PATTERN_REGEX.test(item));
        
      // Remove duplicates
      this.items = [...new Set(this.items)];
      
      this.renderItems();
      this.updateHiddenInput();
    }
  }
  
  disconnectedCallback() {
    // Clean up event listeners when element is removed
    const addButton = this.querySelector('#addButton');
    if (addButton) {
      addButton.removeEventListener('click', this.addItem);
    }
    
    const newItemInput = this.querySelector('#newItem');
    if (newItemInput) {
      newItemInput.removeEventListener('keypress', this.handleKeyPress);
    }
    
    const submitButton = this.querySelector('.submit-btn');
    if (submitButton) {
      submitButton.removeEventListener('click', this.handleSubmit);
    }
  }
}

// Define the custom element
customElements.define('list-input', ListInputElement);
