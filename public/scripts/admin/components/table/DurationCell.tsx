import React from 'react';

interface DurationProps {
    children: string
}

export default function DurationCell(props: DurationProps) {
    return <span className="table__duration">{props.children}</span>
}