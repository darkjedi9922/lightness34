import React from 'react';
import Table from '../table';
import { TableItem } from '../table/item';

interface DatabaseTable {
    name: string
}

interface DatabaseTablesProps {
    tables: DatabaseTable[]
}

class DatabaseTables extends React.Component<DatabaseTablesProps> {
    public render(): React.ReactNode {
        const items: TableItem[] = [];
        this.props.tables.map((table) => {
            items.push({
                cells: [
                    table.name
                ]
            })
        })
        return (
            <div className="box box--table">
                <Table
                    headers={['Name']}
                    items={items}
                ></Table>
            </div>
        );
    }
}

export default DatabaseTables;