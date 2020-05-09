import React from 'react';
import { isNil } from 'lodash';
import classNames from 'classnames';

interface ButtonCellProps {
    href: string,
    icon: string,
    color?: string,
    children: any
}

const ButtonCell = function(props: ButtonCellProps): JSX.Element {
    const classes: (string|object)[] = ['box-actions__item'];
    if (!isNil(props.color)) {
        const color = {};
        color[`box-actions__item--${props.color}`] = true;
        classes.push(color);
    };
    
    return <a href={props.href} className={classNames(classes)}>
        <i className={`box-actions__icon icon-${props.icon}`}></i>
        {props.children}
    </a>
}

export default ButtonCell;