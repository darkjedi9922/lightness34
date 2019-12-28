import React from 'react';
import classNames from 'classnames';

interface Emit {
    id: number
}

interface Handle {
    emits: Emit[]
}

export interface Subscriber {
    event: string,
    class: string,
    handles: Handle[]
}

interface Props {
    subscribers: { [id: number]: Subscriber }
}

class SubscriberList extends React.Component<Props> {
    public render(): React.ReactNode {
        const items = [];
        let index = 0;
        for (const strId in this.props.subscribers) {
            if (this.props.subscribers.hasOwnProperty(strId)) {
                const id = parseInt(strId);
                const subscriber = this.props.subscribers[strId];
                const hasExecutions = subscriber.handles.length;
                const statusClasses = classNames(
                    'status',
                    { 'status--ok': hasExecutions },
                    { 'status--none': !hasExecutions },
                );
                items.push(
                    <div key={id} className="param">
                        <span className="param__number">{++index}</span>
                        <span className="param__name">{subscriber.event}</span>
                        <span className="param__value">{subscriber.class}</span>
                        &nbsp;
                        <span className={statusClasses}>
                            <span className="status__message">
                                (executions: {subscriber.handles.length})
                            </span>
                        </span>
                    </div>
                );
    }
}

export default SubscriberList;