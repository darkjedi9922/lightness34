import React from 'react';
import Table from '../table';
import { TableItem } from '../table/item';
import { isNil } from 'lodash';
import Status, { Type } from '../status';

interface DatabaseTableField {
    name: string,
    type: string,
    null: boolean,
    primary: boolean,
    default: string|number|null
}

interface DatabaseTable {
    name: string,
    fields: DatabaseTableField[],
    rowCount: number
}

interface DatabaseTablesProps {
    tables: DatabaseTable[]
}

class DatabaseTables extends React.Component<DatabaseTablesProps> {
    public render(): React.ReactNode {
        const items: TableItem[] = [];
        this.props.tables.map((table) => {
            const fields: TableItem[] = [];
            table.fields.map((field) => {
                fields.push({
                    cells: [
                        (<>
                            {field.name}&nbsp;
                            {field.primary &&
                                <span className="mark mark--blue">primary</span>
                            }
                            {field.null &&
                                <span className="mark mark--purple">null</span>
                            }
                        </>),
                        field.type,
                        (!isNil(field.default)
                            ? (field.default !== ''
                                ? field.default
                                : <Status
                                    type={Type.EMPTY}
                                    message="empty"
                                ></Status>
                            )
                            : <Status 
                                type={Type.EMPTY}
                                message="no default"
                            ></Status>
                        )
                    ]
                })
            })
            items.push({
                cells: [table.name, fields.length, table.rowCount],
                details: [{
                    content: (
                        <Table
                            headers={['Field', 'Type', 'Default']}
                            items={fields}
                        ></Table>
                    )
                }]
            })
        })
        return (
            <div className="box box--table">
                <Table
                    headers={['Name', 'Fields', 'Rows']}
                    items={items}
                ></Table>
            </div>
        );
    }
}

export default DatabaseTables;