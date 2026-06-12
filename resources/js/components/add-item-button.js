// Constants
const CHECKBOX_LEFT_OFFSET = 36; // Width of the checkbox control in pixels
const INPUT_MIN_WIDTH = 200; // Minimum width for the text input in pixels

// Translations
const TRANSLATIONS = {
  en: {
    itemLabel: 'Item'
  },
  fr: {
    itemLabel: 'Élément'
  }
};

/**
 * Custom element that adds a button to create checkboxes with editable labels
 * @customElement
 */
class AddItemButton extends HTMLElement {
  /**
   * Create a new AddItemButton instance
   */
  constructor() {
    super();
    this.locale = 'fr'; // Default locale
    this.items = []; // Array to track created items
    this.initialItems = []; // Array to store initial items from attribute
  }
  
  /**
   * Called when the element is added to the DOM
   */
  connectedCallback() {
    this.initializeLocale();
    this.setupButton();
    this.attachEventListeners();
    this.loadInitialItems();
  }
  
  /**
   * Initialize the locale based on the HTML lang attribute
   */
  initializeLocale() {
    const htmlLang = document.documentElement.getAttribute('lang');
    this.locale = htmlLang && TRANSLATIONS[htmlLang] ? htmlLang : 'fr';
  }
  
  /**
   * Set up the button properties and attributes
   */
  setupButton() {
    this.setAttribute('role', 'button');
    this.setAttribute('tabindex', '0');
  }
  
  /**
   * Attach event listeners to the button
   */
  attachEventListeners() {
    this.addEventListener('click', this.addItem.bind(this));
    this.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        this.addItem();
      }
    });
  }
  
  /**
   * Load initial items from the 'items' attribute if provided
   */
  loadInitialItems() {
    const itemsAttr = this.getAttribute('items');
    if (itemsAttr) {
      try {
        this.initialItems = JSON.parse(itemsAttr);
        if (Array.isArray(this.initialItems)) {
          // Create each initial item
          this.initialItems.forEach(itemValue => {
            this.addItem(itemValue);
          });
        }
      } catch (error) {
        console.error('Error parsing items attribute:', error);
      }
    }
  }
  
  /**
   * Add a new checkbox item to the list
   * @param {string|Event} [initialValueOrEvent] - Optional initial value for the item or event object
   */
  addItem(initialValueOrEvent) {
    const baseName = this.getAttribute('name') || 'item';
    const itemName = `${baseName}[]`;
    const defaultValue = `${TRANSLATIONS[this.locale].itemLabel} ${this.items.length + 1}`;
    
    // Check if the parameter is an event (like when called from a click event handler)
    const isEvent = initialValueOrEvent instanceof Event;
    const itemValue = !isEvent && initialValueOrEvent ? initialValueOrEvent : defaultValue;
    
    // Create DOM structure
    const itemContainer = this.createItemContainer();
    const checkboxWrapper = this.createCheckboxWrapper();
    const checkbox = this.createCheckbox(itemName, itemValue);
    const displayLabel = this.createDisplayLabel(itemValue);
    const textInput = this.createTextInput(itemValue);
    
    // Add to DOM
    checkbox.appendChild(displayLabel);
    checkbox.appendChild(textInput);
    checkboxWrapper.appendChild(checkbox);
    itemContainer.appendChild(checkboxWrapper);
    
    // Add event listeners
    this.setupCheckboxEvents(checkbox);
    this.setupDisplayLabelEvents(displayLabel, textInput);
    this.setupTextInputEvents(textInput, displayLabel, checkbox);
    
    // Add to container
    const checkboxListContainer = this.getOrCreateCheckboxListContainer();
    checkboxListContainer.appendChild(itemContainer);
    
    // Store references
    this.items.push({
      container: itemContainer,
      checkbox: checkbox,
      textInput: textInput,
      displayLabel: displayLabel
    });
    
    // Initialize display mode for pre-defined items
    displayLabel.style.display = 'inline-block';
    textInput.style.display = 'none';
    
    // For interactively added items (not from attribute), start in edit mode
    if (isEvent || (!initialValueOrEvent && !isEvent)) {
      displayLabel.style.display = 'none';
      textInput.style.display = 'inline';
      
      // Focus the input field (with a slight delay to ensure rendering)
      setTimeout(() => {
        textInput.focus();
      }, 0);
    }
  }
  
  /**
   * Create the container for an individual item
   * @returns {HTMLElement} The item container
   */
  createItemContainer() {
    return document.createElement('div');
  }
  
  /**
   * Create the wrapper for the checkbox with appropriate class
   * @returns {HTMLElement} The checkbox wrapper
   */
  createCheckboxWrapper() {
    const wrapper = document.createElement('div');
    wrapper.classList.add('form__column');
    return wrapper;
  }
  
  /**
   * Create a checkbox element with appropriate attributes
   * @param {string} name - The name attribute for the checkbox
   * @param {string} value - The initial value for the checkbox
   * @returns {HTMLElement} The checkbox element
   */
  createCheckbox(name, value) {
    const checkbox = document.createElement('sl-checkbox');
    checkbox.checked = true;
    checkbox.setAttribute('name', name);
    checkbox.value = value;
    checkbox.setAttribute('value', value);
    return checkbox;
  }
  
  /**
   * Create a display label for the checkbox
   * @param {string} text - The text to display
   * @returns {HTMLElement} The display label
   */
  createDisplayLabel(text) {
    const label = document.createElement('div');
    label.textContent = text;
    label.style.display = 'inline-block';
    return label;
  }
  
  /**
   * Create an input field for editing the label
   * @param {string} value - The initial value for the input
   * @returns {HTMLElement} The text input
   */
  createTextInput(value) {
    const input = document.createElement('input');
    input.type = 'text';
    input.value = value;
    input.style.display = 'none';
    
    // Position absolutely
    input.style.position = 'absolute';
    input.style.left = `${CHECKBOX_LEFT_OFFSET}px`;
    input.style.top = '50%';
    input.style.transform = 'translateY(-50%)';
    input.style.width = 'calc(100% - 40px)';
    input.style.minWidth = `${INPUT_MIN_WIDTH}px`;
    
    return input;
  }
  
  /**
   * Set up events for the checkbox to prevent label clicks from toggling
   * @param {HTMLElement} checkbox - The checkbox element
   */
  setupCheckboxEvents(checkbox) {
    checkbox.addEventListener('click', (event) => {
      // Only allow clicking the actual checkbox part to toggle state
      const rect = checkbox.getBoundingClientRect();
      const clickX = event.clientX - rect.left;
      
      // If click is not on the far left (where the checkbox is)
      // then prevent the default toggle behavior
      if (clickX > CHECKBOX_LEFT_OFFSET) {
        event.preventDefault();
        event.stopPropagation();
      }
    });
  }
  
  /**
   * Set up events for the display label
   * @param {HTMLElement} label - The display label
   * @param {HTMLElement} input - The text input
   */
  setupDisplayLabelEvents(label, input) {
    label.addEventListener('click', (event) => {
      // Prevent event from triggering checkbox toggle
      event.stopPropagation();
      event.preventDefault();
      
      // Immediately switch to edit mode
      setTimeout(() => {
        label.style.display = 'none';
        input.style.display = 'inline';
        input.focus();
      }, 0);
    });
  }
  
  /**
   * Set up events for the text input
   * @param {HTMLElement} input - The text input
   * @param {HTMLElement} label - The display label
   * @param {HTMLElement} checkbox - The checkbox element
   */
  setupTextInputEvents(input, label, checkbox) {
    input.addEventListener('click', (event) => {
      // Prevent event from triggering checkbox toggle
      event.stopPropagation();
    });
    
    input.addEventListener('focus', () => {
      label.style.display = 'none';
      input.style.display = 'inline';
    });
    
    input.addEventListener('blur', () => {
      label.style.display = 'inline';
      input.style.display = 'none';
      label.textContent = input.value;
      checkbox.value = input.value;
      checkbox.setAttribute('value', input.value);
    });
    
    input.addEventListener('input', () => {
      checkbox.value = input.value;
      checkbox.setAttribute('value', input.value);
    });
    
    input.addEventListener('keydown', (event) => {
      // Prevent event from triggering checkbox toggle
      event.stopPropagation();
      if (event.key === 'Enter') {
        input.blur();
      }
    });
  }
  
  /**
   * Get or create the container for the checkbox list
   * @returns {HTMLElement} The checkbox list container
   */
  getOrCreateCheckboxListContainer() {
    const parent = this.parentElement;
    let checkboxListContainer = this.nextElementSibling;
    
    if (!checkboxListContainer || !checkboxListContainer.hasAttribute('data-items-container')) {
      checkboxListContainer = document.createElement('div');
      checkboxListContainer.setAttribute('data-items-container', '');
      parent.insertBefore(checkboxListContainer, this.nextSibling);
    }
    
    return checkboxListContainer;
  }
}

// Define the custom element
customElements.define('add-item-button', AddItemButton);
