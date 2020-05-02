import React from 'react';
import { RadioField } from './Form';
import classNames from 'classnames';

interface FormRadioProps {
    field: RadioField
}

class FormRadio extends React.Component<FormRadioProps> {
    public render(): React.ReactNode {
        return (
            <div className="radio">
                {this.props.field.values.map((value, i) => (
                    <div key={i} className="radio__item">
                        <label className="radio__mark-container">
                            <input
                                type="radio"
                                name={this.props.field.name}
                                value={value.value}
                                defaultChecked={this.props.field.currentValue === value.value}
                                className="radio__input"
                                onChange={this.props.field.onChange}
                            />
                            <i className={classNames([
                                'radio__mark',
                                {'radio__mark--checked': this.props.field.currentValue === value.value},
                                {'radio__mark--unchecked': this.props.field.currentValue !== value.value}
                            ])}></i>
                        </label>
                        <span className="radio__label">{value.label}</span>
                    </div>
                ))}
            </div>
        )
    }
}

export default FormRadio;