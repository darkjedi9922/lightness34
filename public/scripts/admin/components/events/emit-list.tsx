import React from 'react';
import Table, { TableItem } from '../table';
import { Handle, Emit, Subscriber } from 'scripts/admin/structures';
import EmitParameters from './emit-list/emit-parameters';

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
            items.push({
                cells: [
                    <span className="table__number">{i + 1}</span>,
                    emit.event,
                    this.findEmitHandles(emit.id).length
                ],
                details: [{
                    title: 'Parameters',
                    content: <EmitParameters argsJson={emit.argsJson || '[]'} />
                }]
            })
        }

        return (
            <Table
                headers={['Emit', 'Event', 'Handles']} 
                items={items}
            ></Table>
        );
    }

    private findEmitHandles(emitId: number): Handle[] {
        return this.props.handles.filter((handle) => handle.emitId === emitId);
    }
}

export default EmitList;