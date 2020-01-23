import React from 'react';
import ReactDOM from 'react-dom';
import { isNil } from 'lodash';
import $ from 'jquery';
import classNames from 'classnames';
import FormTextInput from './FormTextInput';
import FormRadio from './FormRadio';
import FormTextarea from './FormTextarea';
import FormFileInput from './FormFileInput';
import FormCheckbox from './FormCheckbox';

export interface Field {
    type: string, // it is defined by descendants
    title?: string
    name: string,
    errors?: string[]
}

export interface TextField extends Field
{
    type: 'text'|'password'|'textarea'
    placeholder?: string,
    defaultValue?: string,
}

export interface CheckboxField extends Field {
    type: 'checkbox',
    label?: string,
    defaultChecked?: boolean
}

export interface RadioField extends Field {
    type: 'radio',
    values: RadioValue[],
    currentValue: string
}

export interface FileField extends Field {
    type: 'file'
}

export interface RadioValue {
    label: string,
    value: string
}

interface FormProps {
    actionUrl?: string,
    method: 'get'|'post',
    multipart?: boolean,
    fields: Field[],
    errors?: string[],
    buttonText: string,
    className?: string,
    onSubmit?: (event: React.FormEvent<HTMLFormElement>) => void
}

class Form extends React.Component<FormProps> {
    public constructor(props: FormProps) {
        super(props);
        this.onSubmit = this.onSubmit.bind(this);
    }

    public componentDidMount(): void {
        this.maximizeKeysWidth();
    }

    public render(): React.ReactNode {
        return (
            <form
                className={classNames("form", this.props.className)}
                action={!isNil(this.props.actionUrl) ? this.props.actionUrl : null}
                method={this.props.method}
                encType={this.props.multipart ? 'multipart/form-data' : null}
                onSubmit={this.onSubmit}
            >
                {!isNil(this.props.errors) && this.props.errors.map((error, i) => (
                    <span key={i} className="form__error form__error--full">
                        {error}
                    </span>
                ))}
                {this.props.fields.map((field, i) => (
                    <div key={i} className="form__row">
                        {!isNil(field.title) &&
                            <span className="form__key">{field.title}</span>
                        }
                        <div className="form__field-container">
                            {(field.type === 'text' || field.type === 'password') &&
                                <FormTextInput field={field as TextField} />
                            }
                            {field.type === 'textarea' &&
                                <FormTextarea field={field as TextField} />
                            }
                            {field.type === 'checkbox' &&
                                <FormCheckbox field={field as CheckboxField} />
                            }
                            {field.type === 'radio' &&
                                <FormRadio field={field as RadioField} />
                            }
                            {field.type === 'file' &&
                                <FormFileInput field={field as FileField} />
                            }
                        </div>
                        {!isNil(field.errors) && field.errors.map((error, i) => (
                            <span key={i} className="form__error">{error}</span>
                        ))}
                    </div>
                ))}
                <button className="form__button">{this.props.buttonText}</button>
            </form>
        );
    }

    private maximizeKeysWidth(): void {
        const rootEl = ReactDOM.findDOMNode(this);
        let maxKeyWidth = -1;
        const keyElements = $(rootEl).find("> .form__row > .form__key");
        keyElements.map((index, element) => {
            let width = $(element).width();
            if (width > maxKeyWidth) maxKeyWidth = width;
        })
        if (maxKeyWidth !== -1) keyElements.css('min-width', `${maxKeyWidth}px`);
    }

    private onSubmit(event: React.FormEvent<HTMLFormElement>): void {
        if (isNil(this.props.actionUrl)) {
            event.preventDefault();
            event.stopPropagation();
        }
        if (!isNil(this.props.onSubmit)) this.props.onSubmit(event);
    }
}

export default Form;