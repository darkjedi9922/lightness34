import React from 'react';
import ReactDOM from 'react-dom';
import { isNil } from 'lodash';
import TextareaAutosize from 'react-textarea-autosize';
import $ from 'jquery';
import classNames from 'classnames';
import { decodeHTML } from 'buk';

enum FieldType {
    TEXT = 'text',
    PASSWORD = 'password',
    TEXTAREA = 'textarea'
}

interface Field {
    title?: string
    name: string,
    type: FieldType,
    placeholder?: string,
    defaultValue?: string,
    errors?: string[]
}

interface FormProps {
    actionUrl: string,
    method: 'get'|'post',
    fields: Field[],
    errors?: string[],
    buttonText: string,
    className?: string
}

class Form extends React.Component<FormProps> {
    private textareaRefs: TextareaAutosize[] = [];

    public componentDidMount(): void {
        this.maximizeKeysWidth();

        // Важно использовать именно при window load ибо оказалось, что реакт
        // вызывает componentDidMount ДО этого события >>:C
        // (А я то думал почему все вечно не работало в этом хуке...)
        window.addEventListener('load', () => {
            // Костыль - компонент TextareaAutosize неправильно расчитывает высоту
            // своего текстового поля в самом начале. Так поможем ему! 
            this.textareaRefs.map((textarea) => textarea._resizeComponent());
        });
    }

    public render(): React.ReactNode {
        return (
            <form
                className={classNames("form", this.props.className)}
                action={this.props.actionUrl}
                method={this.props.method}
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
                        {  (field.type === FieldType.TEXT 
                        || field.type === FieldType.PASSWORD) &&
                            <input 
                                type={field.type}
                                name={field.name}
                                className="form__input"
                                placeholder={field.placeholder}
                                defaultValue={decodeHTML(field.defaultValue || '')}
                            />
                        }
                        {field.type === FieldType.TEXTAREA &&
                            <TextareaAutosize
                                ref={(textarea: TextareaAutosize) => {
                                    this.textareaRefs.push(textarea)
                                }}
                                name={field.name}
                                className="form__textarea"
                                placeholder={field.placeholder}
                                defaultValue={decodeHTML(field.defaultValue || '')}
                            />
                        }
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
        console.log(keyElements);
        keyElements.map((index, element) => {
            let width = $(element).width();
            if (width > maxKeyWidth) maxKeyWidth = width;
        })
        if (maxKeyWidth !== -1) keyElements.css('min-width', `${maxKeyWidth}px`);
    }
}

export default Form;