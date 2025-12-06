namespace MiMercado.Api.Models
{
    public class Product
    {
        public int eCodProducto { get; set; }
        public string tNombre { get; set; } = null!;
        public string? tDescripcion { get; set; }
        public decimal dPrecio { get; set; }
        public int eStock { get; set; }
        public int? eCodCategoria { get; set; }
        public string tCodEstatus { get; set; } = "AC";
    }
}
