<% with $DataForYmlList %>
---
Name: $getYmlName
---
<% loop $Classes %>
$ClassName:
    <% loop $Values %>$PropertyName: $DefaultValue # more information<% end_loop %>
<% end_loop %>

<% end_with %>
