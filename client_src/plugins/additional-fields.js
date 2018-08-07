import { components, editPost } from '@frontkom/gutenberg-js';

const { PanelBody } = components;
const { PluginSidebar } = editPost;

export default function AdditionalFieldsPluginSidebar() {
  return (
    <PluginSidebar
      name="additional-fields"
      title="Additional fields"
      icons="forms"
      isPinnable="false"
    >
      <PanelBody />
    </PluginSidebar>
  );
}
