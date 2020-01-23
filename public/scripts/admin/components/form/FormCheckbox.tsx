import React from 'react';
import { CheckboxField } from './Form';
import { isNil } from 'lodash';

interface CheckboxFieldProps {
    field: CheckboxField
}

const FormCheckbox = function(props: CheckboxFieldProps) {
    return (
        <label className="checkbox">
            <input type="hidden" name={props.field.name} value="0"/>
            <input
                className="checkbox__input"
                type="checkbox"
                name={props.field.name}
                defaultChecked={props.field.defaultChecked}
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

export default FormCheckbox;