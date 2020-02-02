import React from 'react';
import { CheckboxField } from './Form';
import { isNil } from 'lodash';
import classNames from 'classnames';

interface CheckboxFieldProps {
    field: CheckboxField
}

class FormCheckbox extends React.Component<CheckboxFieldProps> {
    public render() {
        const props = this.props;
        return (
            <label className={classNames(
                "checkbox",
                {'checkbox--disabled': props.field.disabled}
            )}>
                <input type="hidden" name={props.field.name} value="0"/>
                <input
                    className="checkbox__input"
                    type="checkbox"
                    name={props.field.name}
                    defaultChecked={props.field.defaultChecked}
                    disabled={props.field.disabled}
                    value="1"
                /> 
                <div className="checkbox__box">
                    <i className="checkbox__icon icon-ok"></i>
                </div>
                {!isNil(props.field.label) &&
                    <span className="checkbox__label">{props.field.label}</span>
                }
            </label>
        );
    }
}

export default FormCheckbox;