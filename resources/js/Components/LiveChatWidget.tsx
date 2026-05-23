import { useEffect, useRef } from 'react';

interface LiveChatWidgetProps {
  code?: string | null;
}

export default function LiveChatWidget({ code }: LiveChatWidgetProps) {
  const containerRef = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    const container = containerRef.current;

    if (!container) {
      return;
    }

    container.innerHTML = '';

    if (!code?.trim()) {
      return;
    }

    const template = document.createElement('template');
    template.innerHTML = code;

    Array.from(template.content.childNodes).forEach((node) => {
      if (node.nodeName.toLowerCase() !== 'script') {
        container.appendChild(node.cloneNode(true));
        return;
      }

      const source = node as HTMLScriptElement;
      const script = document.createElement('script');

      Array.from(source.attributes).forEach((attribute) => {
        script.setAttribute(attribute.name, attribute.value);
      });

      script.text = source.text;
      container.appendChild(script);
    });

    return () => {
      container.innerHTML = '';
    };
  }, [code]);

  if (!code?.trim()) {
    return null;
  }

  return <div ref={containerRef} suppressHydrationWarning />;
}
