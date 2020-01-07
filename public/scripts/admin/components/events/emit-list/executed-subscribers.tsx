import React from 'react';
import { Handle, Subscriber } from 'scripts/admin/structures';
import Parameter from '../../parameter';
import Status, { Type } from '../../status';

export interface Execution {
    handle: Handle,
    subscriber: Subscriber
}

interface Props {
    executions: Execution[]
}

class ExecutedEmitSubscribers extends React.Component<Props> {
    public render(): React.ReactNode {
        const hasExecutions = this.props.executions.length !== 0;
        
        if (!hasExecutions) return (
            <Status 
                type={Type.EMPTY} 
                message="No subscribers"
            ></Status>
        );

        return this.props.executions.map((execution, index) => 
            <div key={index} className="executed-subscriber">
                <Parameter
                    number={index + 1}
                    value={execution.subscriber.class}
                ></Parameter>
                <span className="executed-subscriber__duration">
                    {execution.handle.durationSec} sec
                </span>
            </div>
        );
    }
}

export default ExecutedEmitSubscribers;