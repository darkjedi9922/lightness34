import React from 'react';
import { Handle, Subscriber } from 'scripts/admin/components/events/_common';
import Parameter from '../../common/Parameter';
import Status, { Type } from '../../common/Status';

export interface Execution {
    handle: Handle,
    subscriber: Subscriber
}

interface Props {
    executions: Execution[]
}

class ExecutedSubscribers extends React.Component<Props> {
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

export default ExecutedSubscribers;