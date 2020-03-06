import React from 'react';

interface LabelProps {
    color: 'yellow' | 'blue' | 'red' | 'green' | 'purple' | 'grey',
    children: string
}

const Label = (props: LabelProps) => {
    return <span className={`mark mark--${props.color}`}>{props.children}</span>
}

export default Label;