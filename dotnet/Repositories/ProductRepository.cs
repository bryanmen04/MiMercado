using Dapper;
using MiMercado.Api.Models;
using System.Data;

namespace MiMercado.Api.Repositories
{
    public interface IProductRepository
    {
        Task<IEnumerable<Product>> GetAllAsync();
        Task<Product?> GetByIdAsync(int id);
        Task<int> CreateAsync(Product p);
        Task<bool> UpdateAsync(Product p);
        Task<bool> DeleteAsync(int id);
    }

    public class ProductRepository : IProductRepository
    {
        private readonly IDbConnection _db;
        public ProductRepository(IDbConnection db) => _db = db;

        public async Task<IEnumerable<Product>> GetAllAsync()
        {
            var sql = "SELECT eCodProducto, tNombre, tDescripcion, dPrecio, eStock, eCodCategoria, tCodEstatus FROM TblProductos WHERE tCodEstatus='AC'";
            return await _db.QueryAsync<Product>(sql);
        }

        public async Task<Product?> GetByIdAsync(int id)
        {
            var sql = "SELECT eCodProducto, tNombre, tDescripcion, dPrecio, eStock, eCodCategoria, tCodEstatus FROM TblProductos WHERE eCodProducto=@Id";
            return await _db.QueryFirstOrDefaultAsync<Product>(sql, new { Id = id });
        }

        public async Task<int> CreateAsync(Product p)
        {
            var sql = @"INSERT INTO TblProductos (tNombre,tDescripcion,dPrecio,eStock,eCodCategoria,tCodEstatus)
                        VALUES (@tNombre,@tDescripcion,@dPrecio,@eStock,@eCodCategoria,@tCodEstatus);
                        SELECT LAST_INSERT_ID();";
            var id = await _db.ExecuteScalarAsync<int>(sql, p);
            return id;
        }

        public async Task<bool> UpdateAsync(Product p)
        {
            var sql = @"UPDATE TblProductos SET tNombre=@tNombre, tDescripcion=@tDescripcion, dPrecio=@dPrecio, eStock=@eStock, eCodCategoria=@eCodCategoria, tCodEstatus=@tCodEstatus
                        WHERE eCodProducto=@eCodProducto";
            var rows = await _db.ExecuteAsync(sql, p);
            return rows > 0;
        }

        public async Task<bool> DeleteAsync(int id)
        {
            var sql = "DELETE FROM TblProductos WHERE eCodProducto=@Id";
            var rows = await _db.ExecuteAsync(sql, new { Id = id });
            return rows > 0;
        }
    }
}
