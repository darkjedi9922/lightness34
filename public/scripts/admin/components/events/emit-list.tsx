import React from 'react';
import Table from '../table/table';
import { Handle, Emit, Subscriber } from 'scripts/admin/structures';
import EmitParameters from './emit-list/emit-parameters';
import ExecutedEmitSubscribers, { Execution } from './emit-list/executed-subscribers';
import { TableItem } from '../table/item';

interface Props {
    emits: Emit[],
    handles: Handle[],
    subscribers: Subscriber[]
}

class EmitList extends React.Component<Props> {
    public render(): React.ReactNode {
        const items: TableItem[] = [];
        for (let i = 0; i < this.props.emits.length; i++) {
            const emit = this.props.emits[i];
            const emitHandles = this.findEmitHandles(emit.id);
            items.push({
                cells: [
                    <span className="table__number">{i + 1}</span>,
                    emit.event,
                    emitHandles.length
                ],
                details: [{
                    title: 'Parameters',
                    content: <EmitParameters argsJson={emit.argsJson || '[]'} />
                }, {
                    title: 'Executed subrcribers',
                    content: <ExecutedEmitSubscribers
                        executions={this.findEmitExecutions(emit.id)}
                    ></ExecutedEmitSubscribers>
                }]
            })
        }

        return (
            <Table
                headers={['Emit', 'Event', 'Handles']} 
                items={items}
                collapsable={true}
            ></Table>
        );
    }

    private findEmitHandles(emitId: number): Handle[] {
        return this.props.handles.filter((handle) => handle.emitId === emitId);
    }

    private findEmitExecutions(emitId: number): Execution[] {
        const result: Execution[] = [];
        this.props.handles.map((handle) => {
            if (handle.emitId === emitId) result.push({
                handle: handle,
                subscriber: this.props.subscribers.find((subscriber) => {
                    return subscriber.id === handle.subscriberId;
                })
            })
        });
        return result;
    }
}

export default EmitList;