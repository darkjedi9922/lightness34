import React from 'react';
import TextareaAutosize from 'react-textarea-autosize';
import { TextareaField } from './Form';
import { decodeHTML } from 'buk';

interface FormTextareaProps {
    field: TextareaField
}

class FormTextarea extends React.Component<FormTextareaProps> {
    private textareaRef = React.createRef<TextareaAutosize>();

    public componentDidMount(): void {
        // Важно использовать именно при window load ибо оказалось, что реакт
        // вызывает componentDidMount ДО этого события >>:C
        // (А я то думал почему все вечно не работало в этом хуке...)
        window.addEventListener('load', () => {
            // Костыль - компонент TextareaAutosize неправильно расчитывает высоту
            // своего текстового поля в самом начале. Так поможем ему! 
            this.textareaRef.current._resizeComponent();
        });
    }

    public render(): React.ReactNode {
        return (
            <TextareaAutosize
                ref={this.textareaRef}
                name={this.props.field.name}
                className="form__textarea"
                placeholder={this.props.field.placeholder}
                defaultValue={decodeHTML(this.props.field.defaultValue || '')}
                minRows={this.props.field.minRows || 3}
            />
        )
    }
}

export default FormTextarea;