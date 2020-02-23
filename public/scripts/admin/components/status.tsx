import React from 'react'
import { isNil } from 'lodash'

export enum Type {
    EMPTY = 'empty',
    NONE = 'none',
    OK = 'ok',
    WARNING = 'warning',
    ERROR = 'error'
}

interface Props {
    type: Type,
    name?: string,
    message: string,
    hint?: string
}

class Status extends React.Component<Props> {
    public render(): React.ReactNode {
        const hasName = !isNil(this.props.name)
        const hasHint = !isNil(this.props.hint)
        return <span className={`status status--${this.props.type}`}>
            {hasName && <span className="status__name">{this.props.name}</span>}
            <span className="status__message">{this.props.message}</span>
            {hasHint && <span className="status__hint">{this.props.hint}</span>}
        </span>
    }
}

export default Status;