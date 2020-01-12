import React from 'react';
import { RadioField } from './Form';

interface FormRadioProps {
    field: RadioField
}

class FormRadio extends React.Component<FormRadioProps> {
    public render(): React.ReactNode {
        return (
            <div className="radio">
                {this.props.field.values.map((value, i) => (
                    <div key={i} className="radio__item">
                        <input
                            type="radio"
                            name={this.props.field.name}
                            value={value.value}
                            id={`${this.props.field.name}-${value.value}`}
                            defaultChecked={this.props.field
                                .currentValue === value.value
                            }
                            className="radio__input"
                        />
                        <label
                            className="radio__mark-container"
                            htmlFor={`${this.props.field.name}-${value.value}`}
                        >
                            <i className="radio__mark"></i>
                        </label>
                        <span className="radio__label">{value.label}</span>
                    </div>
                ))}
            </div>
        )
    }
}

export default FormRadio;