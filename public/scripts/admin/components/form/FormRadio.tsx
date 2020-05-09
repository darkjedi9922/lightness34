import React from 'react';
import { RadioField } from './Form';
import classNames from 'classnames';

interface FormRadioProps {
    field: RadioField
}

interface FormRadioState {
    value: string
}

class FormRadio extends React.Component<FormRadioProps, FormRadioState> {
    public constructor(props: FormRadioProps) {
        super(props);
        this.state = { value: props.field.currentValue };
    }

    public UNSAFE_componentWillReceiveProps(nextProps: FormRadioProps) {
        if (this.props.field.currentValue !== nextProps.field.currentValue)
            this.setState({ value: nextProps.field.currentValue });
    }

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
                                defaultChecked={this.state.value === value.value}
                                className="radio__input"
                                onChange={(event) => {
                                    this.setState({ value: event.target.value });
                                    this.props.field.onChange && this.props.field.onChange(event);
                                }}
                            />
                            <i className={classNames([
                                'radio__mark',
                                {'radio__mark--checked': this.state.value === value.value},
                                {'radio__mark--unchecked': this.state.value !== value.value}
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