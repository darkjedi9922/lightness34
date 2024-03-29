import React from 'react';
import classNames from 'classnames';
import { Subscriber, Handle } from 'scripts/admin/components/events/_common';
import Parameter from '../common/Parameter';

interface Props {
    subscribers: Subscriber[],
    handles: Handle[]
}

class SubscriberList extends React.Component<Props> {
    public render(): React.ReactNode {
        const items = [];
        for (let i = 0; i < this.props.subscribers.length; i++) {
            const subscriber = this.props.subscribers[i];
            const handles = this.findSubscriberHandles(subscriber.id);
            const hasExecutions = handles.length;
            const statusClasses = classNames(
                'status',
                { 'status--ok': hasExecutions },
                { 'status--none': !hasExecutions },
            );
            items.push(
                <div className="executed-subscriber">
                    <Parameter key={i}
                        number={i + 1} 
                        name={subscriber.event}
                        value={subscriber.class}
                    />
                    &nbsp;
                    <span className={statusClasses}>
                        <span className="status__message">
                            (executions: {handles.length})
                        </span>
                    </span>
                </div>
            );
        }

        return items;
    }

    private findSubscriberHandles(subscriberId: number): Handle[] {
        return this.props.handles.filter((handle) => 
            handle.subscriberId === subscriberId
        );
    }
}

export default SubscriberList;