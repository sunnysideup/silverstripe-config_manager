<% with $DataForYmlList %>---
Name: $Name
---

# below are example values you may use
# for configuration

<% loop $Classes %>
$ClassName:
<% loop $Properties %>  $PropertyName: $DefaultValue.RAW
<% end_loop %>
<% end_loop %>

<% end_with %>
