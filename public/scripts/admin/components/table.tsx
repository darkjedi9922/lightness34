import React from 'react';
import classNames from 'classnames';
import Item, { TableItem } from './table/item';

interface TableProps {
    className?: string
    headers?: string[],
    items: TableItem[],
    collapsable?: boolean
}

class Table extends React.Component<TableProps> {
    public render(): React.ReactNode {
        const tableClasses = classNames('table', this.props.className);
        return (
            <table className={tableClasses}>
                {this.props.headers && this.props.headers.length &&
                    <thead>
                        <tr className="table__headers">
                            {this.props.collapsable &&
                                <td className="table__header"></td>
                            }
                            {this.props.headers.map((name, index) => 
                                <td key={index} className="table__header">
                                    {name}
                                </td>
                            )}
                        </tr>
                    </thead>
                }
                {this.props.items.map((item, index) => 
                    <Item 
                        key={index}
                        item={item}
                        collapsable={this.props.collapsable}
                    ></Item>)
                }
            </table>
        );
    }
}

export default Table;