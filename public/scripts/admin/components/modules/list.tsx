import React from 'react'
import Table from '../table';
import { TableItem } from '../table/item'

interface Module {
    name: string,
    class: string,
    parentModuleName?: string
}

interface ModulesListProps {
    list: Module[]
}

class ModulesList extends React.Component<ModulesListProps> {
    public render(): React.ReactNode {
        return (
            <div className="box box--table">
                <Table
                    headers={['Name', 'Class', 'Parent']}
                    items={this.props.list.map((module) => ({
                        cells: [
                            module.name,
                            module.class,
                            module.parentModuleName
                        ]
                    }))}
                ></Table>
            </div>
        )
    }
}

export default ModulesList;