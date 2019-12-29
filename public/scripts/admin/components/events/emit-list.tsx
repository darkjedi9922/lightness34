import React from 'react';
import Table, { TableItem } from '../table';
import { Handle, Emit } from 'scripts/admin/structures';

interface Props {
    emits: Emit[],
    handles: Handle[]
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
                ]
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