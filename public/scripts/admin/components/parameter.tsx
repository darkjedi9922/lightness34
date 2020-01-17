import React from 'react'
import { isNil } from 'lodash'
import classNames from 'classnames'

interface Props {
    number?: number,
    name?: string,
    value: string,
    type?: string
}

class Parameter extends React.Component<Props> {
    public render(): React.ReactNode {
        const hasNumber = !isNil(this.props.number);
        const hasName = !isNil(this.props.name);
        return <div className="param">
            {hasNumber && <span className="param__number">{this.props.number}</span>}
            {hasName && <span className="param__name">{this.props.name}</span>}
            {!isNil(this.props.type) &&
                <span className="param__type">{this.props.type}</span>
            }
            <span className={this.getParamValueClasses()}>{this.props.value}</span>
        </div>
    }

    private getParamValueClasses(): string {
        return classNames(
            'param__value', 
            {'param__value--empty': this.props.value === ''}
        )
    }
}

export default Parameter;