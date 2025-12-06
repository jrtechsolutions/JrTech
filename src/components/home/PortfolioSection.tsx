import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { ExternalLink, Code, Settings, ArrowRight } from 'lucide-react';
import { Button } from "@/components/ui/button";

const categories = ['Todos', 'Desenvolvimento', 'Infraestrutura'];

const projects = [
  {
    id: 1,
    title: 'Moda Style',
    category: 'Desenvolvimento',
    description: 'E-commerce moderno desenvolvido em React com foco em UX e conversão.',
    image: 'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=600&q=80',
    technologies: ['React', 'JavaScript', 'CSS'],
    link: 'https://modastyle.netlify.app/',
  },
  {
    id: 2,
    title: 'JB Queijos e Laticínios',
    category: 'Desenvolvimento',
    description: 'Plataforma de e-commerce para atendimento e captação de clientes.',
    image: 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=600&q=80',
    technologies: ['React', 'JavaScript', 'HTML'],
    link: 'https://jbqueijoslaticinios.netlify.app/',
  },
  {
    id: 3,
    title: 'Adega Element',
    category: 'Desenvolvimento',
    description: 'Sistema completo de delivery com painel administrativo e gestão de estoque.',
    image: 'https://images.unsplash.com/photo-1516594915697-87eb3b1c14ea?w=600&q=80',
    technologies: ['React', 'Node.js', 'Prisma'],
    link: 'https://adega-element.netlify.app/',
  },
  {
    id: 4,
    title: 'Cida Confeiteira',
    category: 'Desenvolvimento',
    description: 'Landing page e e-commerce para confeitaria artesanal.',
    image: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&q=80',
    technologies: ['React', 'JavaScript', 'CSS'],
    link: 'https://cidabolos.netlify.app/',
  },
  {
    id: 5,
    title: 'Infraestrutura Corporativa',
    category: 'Infraestrutura',
    description: 'Implantação completa de infraestrutura de TI para empresa de médio porte.',
    image: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=600&q=80',
    technologies: ['Windows Server', 'VMware', 'Networking'],
    link: '#',
  },
  {
    id: 6,
    title: 'Migração Cloud',
    category: 'Infraestrutura',
    description: 'Projeto de migração de servidores locais para ambiente AWS.',
    image: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&q=80',
    technologies: ['AWS', 'Docker', 'Kubernetes'],
    link: '#',
  },
];

export default function PortfolioSection() {
  const [activeCategory, setActiveCategory] = useState('Todos');

  const filteredProjects = activeCategory === 'Todos' 
    ? projects 
    : projects.filter(p => p.category === activeCategory);

  return (
    <section id="portfolio" className="relative py-24 bg-slate-900 overflow-hidden">
      {/* Background */}
      <div className="absolute inset-0">
        <div className="absolute inset-0 bg-[linear-gradient(rgba(59,130,246,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(59,130,246,0.02)_1px,transparent_1px)] bg-[size:40px_40px]" />
      </div>

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-12"
        >
          <span className="inline-block px-4 py-1.5 bg-blue-500/10 border border-blue-500/20 rounded-full text-blue-400 text-sm font-medium mb-4">
            Portfólio
          </span>
          <h2 className="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
            Projetos que{' '}
            <span className="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
              entregamos
            </span>
          </h2>
          <p className="text-slate-400 text-lg max-w-2xl mx-auto">
            Conheça alguns dos projetos que desenvolvemos para nossos clientes 
            e veja como podemos ajudar sua empresa.
          </p>
        </motion.div>

        {/* Filter Tabs */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5, delay: 0.2 }}
          className="flex justify-center gap-2 mb-12"
        >
          {categories.map((category) => (
            <button
              key={category}
              onClick={() => setActiveCategory(category)}
              className={`px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-300 ${
                activeCategory === category
                  ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25'
                  : 'bg-slate-800/50 text-slate-400 hover:bg-slate-800 hover:text-white'
              }`}
            >
              {category === 'Desenvolvimento' && <Code className="w-4 h-4 inline mr-2" />}
              {category === 'Infraestrutura' && <Settings className="w-4 h-4 inline mr-2" />}
              {category}
            </button>
          ))}
        </motion.div>

        {/* Projects Grid */}
        <motion.div 
          layout
          className="grid md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <AnimatePresence mode="popLayout">
            {filteredProjects.map((project, index) => (
              <motion.div
                key={project.id}
                layout
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.9 }}
                transition={{ duration: 0.3, delay: index * 0.05 }}
                className="group relative"
              >
                <div className="relative h-full bg-slate-800/50 backdrop-blur-sm rounded-2xl overflow-hidden border border-slate-700/50 hover:border-blue-500/30 transition-all duration-500">
                  {/* Image */}
                  <div className="relative h-48 overflow-hidden">
                    <img
                      src={project.image}
                      alt={project.title}
                      className="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent" />
                    
                    {/* Overlay on Hover */}
                    <div className="absolute inset-0 bg-blue-600/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                      {project.link !== '#' && (
                        <a
                          href={project.link}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="p-3 bg-white rounded-full transform scale-0 group-hover:scale-100 transition-transform duration-300 hover:bg-slate-100"
                        >
                          <ExternalLink className="w-6 h-6 text-blue-600" />
                        </a>
                      )}
                    </div>

                    {/* Category Badge */}
                    <div className="absolute top-4 left-4">
                      <span className="px-3 py-1 bg-slate-900/80 backdrop-blur-sm rounded-full text-xs font-medium text-blue-400 border border-blue-500/20">
                        {project.category}
                      </span>
                    </div>
                  </div>

                  {/* Content */}
                  <div className="p-6">
                    <h3 className="text-lg font-semibold text-white mb-2 group-hover:text-blue-400 transition-colors">
                      {project.title}
                    </h3>
                    <p className="text-slate-400 text-sm mb-4 line-clamp-2">
                      {project.description}
                    </p>

                    {/* Technologies */}
                    <div className="flex flex-wrap gap-2">
                      {project.technologies.map((tech) => (
                        <span
                          key={tech}
                          className="px-2 py-1 bg-slate-700/50 rounded text-xs text-slate-400"
                        >
                          {tech}
                        </span>
                      ))}
                    </div>
                  </div>
                </div>
              </motion.div>
            ))}
          </AnimatePresence>
        </motion.div>

        {/* CTA */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="mt-16 text-center"
        >
          <p className="text-slate-400 mb-6">
            Quer ver seu projeto aqui? Vamos conversar sobre suas ideias.
          </p>
          <Button
            onClick={() => {
              const element = document.querySelector('#contato');
              if (element) element.scrollIntoView({ behavior: 'smooth' });
            }}
            className="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-cyan-500 text-white px-8 py-4 rounded-2xl font-semibold shadow-xl shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-300"
          >
            Iniciar Projeto
            <ArrowRight className="w-5 h-5 ml-2" />
          </Button>
        </motion.div>
      </div>
    </section>
  );
}
