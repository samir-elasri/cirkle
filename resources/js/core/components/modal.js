import $ from "jquery";
import Swal from 'sweetalert2';
import components from "../components";

export default ($component) => new Modal($component);

class Modal {
	constructor($component) {
		this.$el = $component;
		this.script = $.parseJSON(this.$el.find("script[type='application/json']").html());
		const templateId = this.$el.data("modal-template");
		const trigger = this.$el.data('trigger');

		// Le template html
		this.template = $("#" + templateId).html() ?? '';

		// Cleanup des ="" que html crée
		this.regex = new RegExp("=\"\"", "gm");
		this.template = this.template.replace(this.regex, "");

		if (typeof trigger == 'undefined' || trigger === 'manual') {
			this.$el.on('click', this.triggerMod.bind(this));
		} else if (trigger === 'automatic') {
			this.triggerMod(null);
		} else if (trigger === 'form') {
			// The idea here is to wait on form submit and get the name:value pair and add it to values.
			const form = this.$el.find(".modal__form");
			form.on('submit', (e) => {
				e.preventDefault();
				const fields = form.serializeArray();
				for (const field of fields) {
					this.script.values[field.name] = field.value;
				}
				this.triggerMod(e);
			});
		}
	}

	triggerMod(e) {
		if (e) {
			// If a dummy put the trigger on a link, we cancel the link
			e.preventDefault();
		}

		// Fait un call ajax et remplace les tags {} par le json retournant
		const {
			ajaxValues
		} = this.script;
		if (ajaxValues) {
			// Cache le bouton en attandant la réponse ajax;
			Swal.showLoading()
			this.$el.hide();
			const self = this;
			$.ajax({
				url: ajaxValues,
				type: "get",
				success(res) {
					// Remplace les tags {} par les valeurs de la réponse ajax
					$.each(res, (k, v) => {
						self.regex = new RegExp("_{" + k + "}", "gm");
						self.template = self.template.replace(self.regex, v);
					});
				},
				error(xhr) {
					console.warn(xhr.responseText);
				}
			}).then(() => {
				this.$el.show();
				this.replaceAndOpen();
			});
		} else {
			this.replaceAndOpen();
		}
	}

	replaceAndOpen() {
		// Remplace les {templates} par les valeurs set en Front End
		$.each(this.script.values, (k, v) => {
			this.regex = new RegExp("_{" + k + "}", "gm");
			this.template = this.template.replace(this.regex, v);
		});
		this.regex = new RegExp("_{\\S*}", "gm");
		this.template = this.template.replace(this.regex, "");

		// Set content and component initializer
		this.script.options.html = this.template;
		this.script.options.willOpen = (modalElement) => {
			components(modalElement);
		};

		Swal.fire(
			this.script.options
		).then((result) => {
			if (result.value) {
				const { href } = this.script;

				if (href) {
					window.location.href = href;
				}
			}
		});
	}
}
