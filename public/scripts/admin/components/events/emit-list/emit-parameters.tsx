import React from 'react';
import classNames from 'classnames';
import { size, isObject, isString, isBoolean, isNull } from 'lodash';

interface Props {
    argsJson: string
}

class EmitParameters extends React.Component<Props> {
    public render(): React.ReactNode {
        return this.createParameterElements(JSON.parse(this.props.argsJson));
    }

    private createParameterElements(args: object): React.ReactNode {
        if (!size(args)) {
            return (
                <div className="param">
                    <span className="param__value param__value--empty">
                        No parameters
                    </span>    
                </div>
            );
        }
        
        const parameters = [];
        for (const key in args) {
            if (args.hasOwnProperty(key)) {
                const arg = args[key];

                const isEmpty = isString(arg) && !arg.length
                             || isObject(arg) && !size(arg);

                let strValue: string = null;
                if (isObject(arg)) strValue = null;
                else if (isNull(arg)) strValue = 'null';
                else strValue = arg.toString();

                const paramValueClasses = classNames(
                    'param__value',
                    {'param__value--empty': isEmpty || isNull(arg) || isBoolean(arg)}
                );
                parameters.push(
                    <div key={key} className="param">
                        <span className="param__name">{key}</span>
                        <span className="param__divisor"> => </span>
                        <span className={paramValueClasses}>
                            {isEmpty 
                            ? ('empty' + (isObject(arg) ? ' array' : ''))
                            : (isObject(arg)
                                ? <>[
                                    <div className="table__details details">
                                        {this.createParameterElements(arg)}
                                    </div>
                                ]</>
                                : strValue
                            )}
                        </span>
                    </div>
                );
            }
        }
        return parameters;
    }
}

export default EmitParameters;