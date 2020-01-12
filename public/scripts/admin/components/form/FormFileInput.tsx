import React from 'react';
import { FileField } from './Form';

interface FormFileInputProps {
    field: FileField
}

interface FormFileInputState {
    selectedFilename: string
}

class FormFileInput extends React.Component<FormFileInputProps, FormFileInputState> {
    public constructor(props: FormFileInputProps) {
        super(props);
        this.state = {
            selectedFilename: ''
        }

        this.onChange = this.onChange.bind(this);
    }
    
    public render(): React.ReactNode {
        return (
            <div className="file-input">
                <label className="file-input__button button">
                    Выбрать...
                    <input 
                        type="file"
                        style={{display: 'none'}}
                        name={this.props.field.name}
                        onChange={this.onChange}
                    ></input>
                </label>
                <input 
                    type="text"
                    className="form__input"
                    value={this.state.selectedFilename}
                    readOnly={true}
                ></input>
            </div>
        )
    }

    private onChange(event: React.ChangeEvent<HTMLInputElement>): void {
        let value = event.target.value.split('\\')[2];
        this.setState({
            selectedFilename: value ? value : ''
        });
    }
}

export default FormFileInput;