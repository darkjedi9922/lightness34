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
        
        if (!hasExecutions) return <Status 
            type={Type.EMPTY} 
            message="No subscribers"
        ></Status>;

        return this.props.executions.reverse().map((execution, index) => 
            <Parameter
                key={index}
                number={index + 1}
                value={execution.subscriber.class}
            ></Parameter>
        );
    }
}

export default ExecutedEmitSubscribers;