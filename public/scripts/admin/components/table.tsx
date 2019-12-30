import React from 'react';
import classNames from 'classnames';

export interface ItemDetails {
    title?: string,
    content: any
}

export interface TableItem {
    cells: any[]
    details?: ItemDetails[]
}

interface TableProps {
    className?: string
    headers?: string[],
    items: TableItem[]
}

class Table extends React.Component<TableProps> {
    public render(): React.ReactNode {
        const tableClasses = classNames('table', this.props.className);
        return (
            <table className={tableClasses}>
                {this.props.headers && this.props.headers.length &&
                    <thead>
                        <tr className="table__headers">
                            {this.props.headers.map((name, index) => 
                                <td key={index} className="table__header">
                                    {name}
                                </td>
                            )}
                        </tr>
                    </thead>
                }
                {this.props.items.map((item, index) => 
                    <tbody key={index} className="table__item-wrapper">
                        <tr className="table__item">
                            {item.cells.map((value, index) => 
                                <td key={index} className="table__cell">
                                    {value}
                                </td>
                            )}
                        </tr>
                        {item.details &&
                            <tr className="table__details-wrapper">
                                <td className="table__details" colSpan={100}>
                                    {item.details.map((details, index) => 
                                        <div key={index} className="details">
                                            {details.title &&
                                                <span className="details__header">
                                                    {details.title}
                                                </span>
                                            }
                                            <div className="details__content">
                                                {details.content}
                                            </div>
                                        </div>
                                    )}
                                </td>
                            </tr>
                        }
                    </tbody>
                )}
            </table>
        );
    }
}

export default Table;