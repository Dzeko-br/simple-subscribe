(function (window) {
    'use strict';
    
    if (window.SimpleSubscribe) {
        return;
    }

    window.SimpleSubscribe = function (params) {
        let error = 0;
        this.settings = {
            ajax: false,
            id: null,
            url: '',
            form: null,
            componentName: null,
            signedParameters: null,
        };

        if (params.id) {
            this.settings.id = params.id;
            this.settings.form = document.getElementById(params.id);
            this.settings.componentName = params.componentName;
            this.settings.signedParameters = params.signedParameters;
        }

        if (!this.settings.form) {
            error = 1;
        }

        if (params.url) {
            this.settings.url = params.url;
        }

        if (error === 0) {
            BX.ready(BX.delegate(this.init, this));
        } else {
            console.log({
                SimpleSubscribe: params.id,
                error: error,
            });
        }
    };

    window.SimpleSubscribe.prototype = {
        init: function () {
            if (window.JustValidate) {
                this.validateForm(this.settings.form, 'submit');
            }
        },

        validateForm: function(form, eventType) {
            const formValidator = new window.JustValidate(form);
            formValidator
                .addField(this.settings.form.querySelector('[name="form[EMAIL]"]'), [
                    {
                        rule: 'required',
                        errorMessage: 'Обязательное поле',
                    },
                    {
                        rule: 'email',
                        errorMessage: 'Некорректный Email',
                    },
                ],
                {
                    errorFieldCssClass: 'error',
                    errorLabelCssClass: 'form-label-error',
                }
            )
            .onSuccess(( event ) => {
                this.sendForm(event.currentTarget);
            });
        },

        sendForm: function(formSubscribe) {
            const self = this;
            const form = new FormData(formSubscribe);

            BX.ajax.runComponentAction(
                self.settings.componentName,
                'submit',
                {
                    mode: 'class',
                    signedParameters: self.settings.signedParameters,
                    data: form,
                }
            )
            .then(function(response) {
                if (response.status === 'success') {
                    self.createResult(response);
                }
            }, function (response) {
                console.log(response);
            });
        },

        createResult: function(data) {
            let wrap = this.settings.form.querySelector('.row');
            const resultMessage = document.createElement('div');
            resultMessage.classList.add('col-12');
            const text = document.createElement('div');
            text.classList.add('subscribe__message', `subscribe__message--${data.status}`)
            text.textContent = data.data.message;
            resultMessage.append(text);
            wrap?.append(resultMessage);
        }
    }
})(window);
