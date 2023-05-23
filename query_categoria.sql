select * from apitest.new_table where id_sub_sub_categoria IS NOT NULL;
UPDATE apitest.new_table nt LEFT JOIN sub_sub_categorias ca ON nt.categoria3 = ca.nombre
SET nt.id_sub_sub_categoria = ca.id;