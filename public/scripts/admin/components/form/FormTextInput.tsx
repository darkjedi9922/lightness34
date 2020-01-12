import React from 'react';
import { TextField } from './Form';
import { decodeHTML } from 'buk';

interface FormTextInputProps {
    field: TextField
}

class FormTextInput extends React.Component<FormTextInputProps> {
    public render(): React.ReactNode {
        return (
            <input
                type={this.props.field.type}
                name={this.props.field.name}
                className="form__input"
                placeholder={this.props.field.placeholder}
                defaultValue={decodeHTML(this.props.field.defaultValue || '')}
            />
        )
    }
}

export default FormTextInput;