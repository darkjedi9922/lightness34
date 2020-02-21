import React from 'react';

interface LoadingContentProps {
    children?: any
}

export default function LoadingContent(props: LoadingContentProps) {
    return props.children || 
        <div className="centered-wrapper centered-wrapper--fixed">
            <i className="icon-spin1 animate-spin content__loading" />
        </div>;
}