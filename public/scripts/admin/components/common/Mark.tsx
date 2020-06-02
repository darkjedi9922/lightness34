import React from 'react';
import { isNil } from 'lodash';
import classNames from 'classnames';
import { MarkColor } from './_common';

interface MarkProps {
    icon?: string,
    label: string,
    color: MarkColor,
    className?: string
}

const Mark = function(props: MarkProps) {
    return (
        <span className={classNames(
            'mark2',
            `mark2--${props.color}`,
            props.className
        )}>
            {!isNil(props.icon) &&
                <i className={`mark2__icon icon-${props.icon}`}></i>
            }
            <span className="mark2__label">{props.label}</span>
        </span>
    );
}

export default Mark;