import React from 'react';

export interface Subscriber {
    event: string,
    class: string
}

interface Props {
    subscribers: { [id: number]: Subscriber }
}

class SubscriberList extends React.Component<Props> {
    public render(): React.ReactNode {
        return (<>
            {Object.keys(this.props.subscribers).map((strId, index) => {
                const id = parseInt(strId);
                const subscriber = this.props.subscribers[id];
                return (
                    <div key={index} className="param">
                        <span className="param__number">{index + 1}</span>
                        <span className="param__name">{subscriber.event}</span>
                        <span className="param__value">{subscriber.class}</span>
                    </div>
                );
            })}
        </>)
    }
}

export default SubscriberList;