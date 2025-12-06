using Microsoft.AspNetCore.Mvc;
using MiMercado.Api.Models;
using MiMercado.Api.Services;

namespace MiMercado.Api.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    [Produces("application/json")]
    public class ProductsController : ControllerBase
    {
        private readonly IProductService _svc;
        public ProductsController(IProductService svc) => _svc = svc;

        /// <summary>Lista todos los productos activos</summary>
        [HttpGet]
        public async Task<IActionResult> GetAll()
        {
            var items = await _svc.GetAll();
            return Ok(new { success = true, data = items });
        }

        /// <summary>Obtiene un producto por id</summary>
        [HttpGet("{id:int}")]
        public async Task<IActionResult> Get(int id)
        {
            var item = await _svc.Get(id);
            if (item == null) return NotFound(new { success = false, message = "Producto no encontrado" });
            return Ok(new { success = true, data = item });
        }

        /// <summary>Crea un nuevo producto</summary>
        [HttpPost]
        public async Task<IActionResult> Create([FromBody] Product p)
        {
            if (p == null) return BadRequest(new { success = false, message = "Payload inválido" });
            var id = await _svc.Create(p);
            return CreatedAtAction(nameof(Get), new { id = id }, new { success = true, insert_id = id });
        }

        /// <summary>Actualiza producto</summary>
        [HttpPut("{id:int}")]
        public async Task<IActionResult> Update(int id, [FromBody] Product p)
        {
            if (p == null || id != p.eCodProducto) return BadRequest(new { success = false, message = "Payload inválido" });
            var ok = await _svc.Update(p);
            if (!ok) return NotFound(new { success = false, message = "Producto no encontrado" });
            return NoContent();
        }

        /// <summary>Elimina producto</summary>
        [HttpDelete("{id:int}")]
        public async Task<IActionResult> Delete(int id)
        {
            var ok = await _svc.Delete(id);
            if (!ok) return NotFound(new { success = false, message = "Producto no encontrado" });
            return NoContent();
        }
    }
}
