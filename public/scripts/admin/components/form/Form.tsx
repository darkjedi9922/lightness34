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
    type: 'text'|'password'|'textarea',
    placeholder?: string,
    defaultValue?: string,
}

export interface TextareaField extends TextField {
    type: 'textarea',
    minRows?: number
}

export interface CheckboxField extends Field {
    type: 'checkbox',
    label?: string,
    defaultChecked?: boolean,
    disabled?: boolean
}

export interface RadioField extends Field {
    type: 'radio',
    values: RadioValue[],
    currentValue: string,
    onChange?: (event: React.ChangeEvent<HTMLInputElement>) => void
}

export interface FileField extends Field {
    type: 'file'
}

export interface Group {
    type: 'group',
    title: string,
    fields: Field[]
}

export interface RadioValue {
    label: string,
    value: string
}

interface FormProps {
    actionUrl?: string,
    method: 'get'|'post',
    multipart?: boolean,
    fields: (Field|Group)[],
    errors?: string[],
    short?: boolean,
    buttonText?: string,
    className?: string,
    onSubmit?: (event: React.FormEvent<HTMLFormElement>, form?: Form) => void
}

class Form extends React.Component<FormProps> {
    private fields: {[name: string]: React.Component} = {};

    public constructor(props: FormProps) {
        super(props);
        this.onSubmit = this.onSubmit.bind(this);
    }

    public componentDidMount(): void {
        window.addEventListener('load', () => {
            this.maximizeKeysWidth();
        });
    }

    public render(): React.ReactNode {
        this.fields = {};
        return (
            <form
                className={classNames(
                    "form",
                    this.props.className,
                    {'form--short': this.props.short}
                )}
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
                {this.renderFields(this.props.fields)}
                {!isNil(this.props.buttonText) &&
                    <button className="form__button">{this.props.buttonText}</button>
                }
            </form>
        );
    }

    public getField<T extends React.Component>(name: string): T {
        return this.fields[name] as T;
    }

    private renderFields(fields: (Field|Group)[]) {
        return fields.map((field, i) => (
            field.type === 'group'
            ? <div key={i} className="form__row form__row--group">
                <span className="form__title">{field.title}</span>
                {this.renderFields((field as Group).fields)}
            </div>
            :<div key={i} className="form__row">
                {!isNil(field.title) &&
                    <span className={classNames(
                        "form__key",
                        {'form__key--empty': !field.title.length}
                    )}>{field.title}</span>
                }
                <div className="form__field-container">
                    {(() => {
                        let el: JSX.Element;
                        const addField = (ref: React.Component) =>
                            this.fields[(field as any).name] = ref;
                        switch (field.type) {
                            case 'text':
                            case 'password':
                                el = <FormTextInput
                                    ref={addField}
                                    field={field as TextField}
                                />
                                break;
                            case 'textarea':
                                el = <FormTextarea
                                    ref={addField}
                                    field={field as TextareaField}
                                />
                                break;
                            case 'checkbox':
                                el = <FormCheckbox
                                    ref={addField}
                                    field={field as CheckboxField}
                                />
                                break;
                            case 'radio':
                                el = <FormRadio
                                    ref={addField}
                                    field={field as RadioField}
                                />
                                break;
                            case 'file':
                                el = <FormFileInput
                                    ref={addField}
                                    field={field as FileField}
                                />
                                break;
                        }
                        return el;
                    })()}
                </div>
                {!isNil((field as Field).errors) && (field as Field).errors
                .map((error, i) =>
                    <span key={i} className="form__error">{error}</span>
                )}
            </div>
        ))
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
        if (!isNil(this.props.onSubmit)) this.props.onSubmit(event, this);
    }
}

export default Form;