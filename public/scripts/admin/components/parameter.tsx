import React from 'react'
import { isNil } from 'lodash'
import classNames from 'classnames'

interface Props {
    number?: number,
    name?: string,
    value: string,
    type?: string,
    nameIsStrong?: boolean,
    divisor?: string,
    empty?: boolean
}

class Parameter extends React.Component<Props> {
    public render(): React.ReactNode {
        const hasNumber = !isNil(this.props.number);
        const hasName = !isNil(this.props.name);
        return <div className="param">
            {hasNumber && <span className="param__number">{this.props.number}</span>}
            {hasName && <>
                <span className={classNames(
                    "param__name",
                    {"param__name--strong": this.props.nameIsStrong}
                )}>{this.props.name}</span>
                <span className={classNames(
                    "param__divisor",
                    {"param__name--strong-name": this.props.nameIsStrong}
                )}>{!isNil(this.props.divisor)
                    ? this.props.divisor
                    : ' => '
                }</span>
            </>}
            {!isNil(this.props.type) &&
                <span className="param__type">{this.props.type}</span>
            }
            <span className={classNames(
                'param__value',
                { 'param__value--empty': this.isEmptyStyled() }
            )}>{this.getValue()}</span>
        </div>
    }

    private getValue(): string {
        return this.props.value !== '' ? this.props.value : 'empty';
    }

    private isEmptyStyled(): boolean {
        return this.props.value === '' || this.props.empty;
    }
}

export default Parameter;