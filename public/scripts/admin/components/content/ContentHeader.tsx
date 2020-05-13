import React from 'react';

const ContentHeader = (props) => (
    <div className="content__header content-header">{props.children}</div>
);

export function ContentHeaderGroup(props) {
    return <div className="content-header__group">{props.children}</div>
};

export default ContentHeader;