import React from 'react';

interface ButtonProps {
    href: string
    children?: any
}

const Button = (props: ButtonProps) => {
    return <a href={props.href} className="button">{props.children}</a>
}

export default Button;